<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

use Zol\Apifony\OpenApi;
use Zol\Apifony\OpenApi\Exception;
use Zol\Apifony\OpenApi\Reference;

/**
 * Turns a parsed specification into one where every '$ref' has been followed, so that the
 * generation layer never holds a Reference and never needs the components object to make sense of
 * what it is looking at.
 *
 * A reference is treated as a leaf: resolving it records the name it points at and stops. The
 * lookup happens on first use, through SchemaRef::getTarget(). That is what makes a recursive
 * specification resolvable at all — building the graph never follows a reference, so the walk is
 * bounded by the parse tree, which is finite and acyclic. The resulting graph is cyclic wherever
 * the specification is, and its nodes are shared: two references to one components schema hand
 * back the same object.
 *
 * It is also what keeps a dangling reference failing at the moment it failed before, when the
 * generator reaches the slot, rather than earlier.
 */
final class Resolver
{
    /**
     * @var array<string, Schema>
     */
    private array $componentSchemas = [];

    /**
     * Inline schemas, keyed by the identity of the parse node they came from, so that the resolved
     * tree is a one to one image of the parsed one even though several passes walk it
     * independently.
     *
     * @var array<int, Schema>
     */
    private array $inlineSchemas = [];

    /**
     * Every reference met while building, in the order it was met, waiting for its target.
     *
     * @var list<SchemaRef>
     */
    private array $pendingReferences = [];

    private function __construct(
        private readonly ?OpenApi\Components $components,
    ) {
    }

    /**
     * @throws Exception
     */
    public static function resolve(OpenApi\OpenApi $openApi): Document
    {
        $resolver = new self($openApi->components);

        $document = new Document(
            $openApi->components !== null ? $resolver->buildComponents($openApi->components) : null,
            $openApi->paths !== null ? $resolver->buildPaths($openApi->paths) : null,
            $openApi->extensions,
            $openApi->path,
        );

        $resolver->bindReferences();

        return $document;
    }

    /**
     * Closes every reference once the whole document has been walked, which is what makes reading
     * the graph infallible afterwards. A schema reference is the only kind that can still be
     * dangling at this point: the other buckets are looked up as they are met, because resolving
     * them has to produce a value rather than a placeholder.
     *
     * @throws Exception
     */
    private function bindReferences(): void
    {
        foreach ($this->pendingReferences as $reference) {
            $name = (string) $reference->componentName;
            if (!isset($this->componentSchemas[$name])) {
                throw new Exception('Reference not found in schemas components.', $reference->path);
            }

            $reference->bind($this->componentSchemas[$name]);
        }
    }

    /**
     * @throws Exception
     */
    private function buildComponents(OpenApi\Components $components): Components
    {
        // The schemas come first and whole, so that every reference met afterwards resolves
        // against a bucket that is already complete.
        foreach ($components->schemas as $name => $schema) {
            $this->componentSchemas[$name] = $this->buildSchema($schema);
        }

        $responses = [];
        foreach ($components->responses as $name => $response) {
            $responses[$name] = $this->buildResponse($response);
        }

        $parameters = [];
        foreach ($components->parameters as $name => $parameter) {
            $parameters[$name] = $this->buildParameter($parameter);
        }

        $requestBodies = [];
        foreach ($components->requestBodies as $name => $requestBody) {
            $requestBodies[$name] = $this->buildRequestBody($requestBody);
        }

        $headers = [];
        foreach ($components->headers as $name => $header) {
            $headers[$name] = $this->buildHeader($header);
        }

        return new Components(
            $this->componentSchemas,
            $responses,
            $parameters,
            $requestBodies,
            $headers,
            $components->extensions,
            $components->path,
        );
    }

    /**
     * @throws Exception
     */
    private function buildPaths(OpenApi\Paths $paths): Paths
    {
        $pathItems = [];
        foreach ($paths->pathItems as $route => $pathItem) {
            $parameters = [];
            foreach ($pathItem->parameters as $parameter) {
                $parameters[] = $this->resolveParameter($parameter);
            }

            $operations = [];
            foreach ($pathItem->operations as $method => $operation) {
                if (self::isIgnored($operation)) {
                    continue;
                }
                $operations[$method] = $this->buildOperation($operation);
            }

            $pathItems[$route] = new PathItem($parameters, $operations, $pathItem->extensions, $pathItem->path);
        }

        return new Paths($pathItems, $paths->extensions, $paths->path);
    }

    /**
     * An operation the specification asks to skip is left out of the resolved document entirely,
     * rather than skipped again by each pass that walks it. Nothing downstream has to remember it
     * exists: no controller, no route, and no validator emitted for a format only it mentions.
     *
     * @throws Exception
     */
    private static function isIgnored(OpenApi\Operation $operation): bool
    {
        if (!\array_key_exists('x-apifony-ignore', $operation->extensions)) {
            return false;
        }
        if (!\is_bool($operation->extensions['x-apifony-ignore'])) {
            throw new Exception('Operation x-apifony-ignore attribute must be a bool.', $operation->path);
        }

        return $operation->extensions['x-apifony-ignore'];
    }

    /**
     * @throws Exception
     */
    private function buildOperation(OpenApi\Operation $operation): Operation
    {
        $parameters = [];
        foreach ($operation->parameters as $parameter) {
            $parameters[] = $this->buildParameter($parameter);
        }

        $requestBody = $operation->requestBody;
        $responses = $operation->responses;

        return new Operation(
            $operation->operationId,
            $parameters,
            $requestBody !== null ? $this->resolveRequestBody($requestBody) : null,
            $responses !== null ? $this->buildResponses($responses) : null,
            $operation->extensions,
            $operation->path,
        );
    }

    /**
     * @throws Exception
     */
    private function buildResponses(OpenApi\Responses $responses): Responses
    {
        $resolved = [];
        foreach ($responses->responses as $code => $response) {
            $resolved[$code] = $this->resolveResponse($response);
        }

        return new Responses($resolved, $responses->extensions, $responses->path);
    }

    /**
     * @throws Exception
     */
    private function buildResponse(OpenApi\Response $response): Response
    {
        $headers = [];
        foreach ($response->headers as $name => $header) {
            $headers[$name] = $this->resolveHeader($header);
        }

        return new Response(
            $headers,
            $this->buildContent($response->content),
            $response->extensions,
            $response->path,
        );
    }

    /**
     * @throws Exception
     */
    private function buildRequestBody(OpenApi\RequestBody $requestBody): RequestBody
    {
        return new RequestBody(
            $requestBody->required,
            $this->buildContent($requestBody->content),
            $requestBody->extensions,
            $requestBody->path,
        );
    }

    /**
     * @param array<string, OpenApi\MediaType> $content
     *
     * @return array<string, MediaType>
     *
     * @throws Exception
     */
    private function buildContent(array $content): array
    {
        $resolved = [];
        foreach ($content as $type => $mediaType) {
            $resolved[$type] = new MediaType(
                $mediaType->schema !== null ? $this->resolveSchema($mediaType->schema) : null,
                $mediaType->extensions,
                $mediaType->path,
            );
        }

        return $resolved;
    }

    /**
     * @throws Exception
     */
    private function buildParameter(OpenApi\Parameter $parameter): Parameter
    {
        return new Parameter(
            $parameter->name,
            $parameter->in,
            $parameter->required,
            $parameter->schema !== null ? $this->resolveSchema($parameter->schema) : null,
            $parameter->extensions,
            $parameter->path,
        );
    }

    /**
     * @throws Exception
     */
    private function buildHeader(OpenApi\Header $header): Header
    {
        return new Header(
            $header->schema !== null ? $this->resolveSchema($header->schema) : null,
            $header->extensions,
            $header->path,
        );
    }

    /**
     * @throws Exception
     */
    private function resolveParameter(Reference|OpenApi\Parameter $parameter): Parameter
    {
        if ($parameter instanceof Reference) {
            if ($this->components === null || !isset($this->components->parameters[$parameter->getName()])) {
                throw new Exception('Reference not found in parameters components.', $parameter->path);
            }

            return $this->buildParameter($this->components->parameters[$parameter->getName()]);
        }

        return $this->buildParameter($parameter);
    }

    /**
     * @throws Exception
     */
    private function resolveHeader(Reference|OpenApi\Header $header): Header
    {
        if ($header instanceof Reference) {
            if ($this->components === null || !isset($this->components->headers[$header->getName()])) {
                throw new Exception('Reference not found in headers components.', $header->path);
            }

            return $this->buildHeader($this->components->headers[$header->getName()]);
        }

        return $this->buildHeader($header);
    }

    /**
     * @throws Exception
     */
    private function resolveRequestBody(Reference|OpenApi\RequestBody $requestBody): RequestBody
    {
        if ($requestBody instanceof Reference) {
            if ($this->components === null || !isset($this->components->requestBodies[$requestBody->getName()])) {
                throw new Exception('Reference not found in requestBodies components.', $requestBody->path);
            }

            return $this->buildRequestBody($this->components->requestBodies[$requestBody->getName()]);
        }

        return $this->buildRequestBody($requestBody);
    }

    /**
     * @throws Exception
     */
    private function resolveResponse(Reference|OpenApi\Response $response): Response
    {
        if ($response instanceof Reference) {
            if ($this->components === null || !isset($this->components->responses[$response->getName()])) {
                throw new Exception('Reference not found in responses components.', $response->path);
            }

            return $this->buildResponse($this->components->responses[$response->getName()]);
        }

        return $this->buildResponse($response);
    }

    /**
     * A reference becomes a leaf carrying the name it points at; an inline schema is built, once
     * per parse node.
     *
     * @throws Exception
     */
    private function resolveSchema(Reference|OpenApi\Schema $schema): SchemaRef
    {
        if ($schema instanceof Reference) {
            return $this->pendingReferences[] = SchemaRef::reference($schema->getName(), $schema->path);
        }

        return SchemaRef::inline($this->buildSchema($schema));
    }

    /**
     * @throws Exception
     */
    private function buildSchema(OpenApi\Schema $schema): Schema
    {
        $id = spl_object_id($schema);
        if (isset($this->inlineSchemas[$id])) {
            return $this->inlineSchemas[$id];
        }

        $properties = [];
        foreach ($schema->properties as $name => $property) {
            $properties[$name] = $this->resolveSchema($property);
        }

        return $this->inlineSchemas[$id] = new Schema(
            $schema->type,
            $schema->format,
            $schema->enum,
            $schema->hasDefault,
            $schema->default,
            $schema->pattern,
            $schema->minLength,
            $schema->maxLength,
            $schema->multipleOf,
            $schema->minimum,
            $schema->maximum,
            $schema->exclusiveMinimum,
            $schema->exclusiveMaximum,
            $schema->items !== null ? $this->resolveSchema($schema->items) : null,
            $schema->minItems,
            $schema->maxItems,
            $schema->uniqueItems,
            $properties,
            $schema->required,
            $schema->extensions,
            $schema->path,
        );
    }
}
