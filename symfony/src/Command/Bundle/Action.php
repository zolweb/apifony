<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Parameter;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class Action
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        Operation $operation,
        Components $components,
    ): self {
        return new self(
            $className = u($operation->operationId)->camel(),
            $parameters = self::buildParameters($className, $operation, $components),
            self::buildCases($bundleNamespace, $aggregateName, $className, $operation, $components, $parameters),
        );
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionCase> $cases
     */
    private function __construct(
        private readonly string $name,
        private readonly array $parameters,
        private readonly array $cases,
    ) {
    }

    /**
     * @return array<ActionParameter>
     */
    public function getParameters(array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_filter(
            $this->parameters,
            static fn (Parameter $param) => in_array($param->parameter->in, $in, true),
        );
    }

    /**
     * @return array<ActionCase>
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * @return array<ActionParameter>
     *
     * @throws Exception
     */
    private static function buildParameters(
        string $actionClassName,
        Operation $operation,
        Components $components,
    ): array {
        $parameters = array_map(
            static fn (Parameter $parameter) =>
            ActionParameter::build($actionClassName, $parameter, $components),
            $operation->parameters,
        );

        $ordinal = 0;
        $ordinals = [];
        foreach ($operation->parameters as $parameter) {
            $ordinals[$parameter->name] = ++$ordinal;
        }

        usort(
            $parameters,
            static fn (ActionParameter $param1, ActionParameter $param2) =>
            ((int)$param1->hasDefault() - (int)$param2->hasDefault()) ?:
                ($ordinals[$param1->getRawName()] - $ordinals[$param2->getRawName()]),
        );

        return $parameters;
    }

    /**
     * @return array<ActionCase>
     * @return array<ActionParameter>
     *
     * @throws Exception
     */
    private static function buildCases(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        Components $components,
        array $parameters,
    ): array {
        $allPossibleRequestBodyPayloadTypes = [];
        $allPossibleResponseContentTypes = [];

        if ($operation->requestBody === null || !$operation->requestBody->required) {
            $allPossibleRequestBodyPayloadTypes['Empty'] = null;
        }

        foreach ($operation->requestBody?->mediaTypes ?? [] as $mediaType) {
            $className = "{$actionClassName}RequestBodyPayload";
            if ($mediaType->schema === null) {
                throw new Exception('MediaTypes without schema are not supported.');
            }
            $schema = $mediaType->schema;
            if ($schema instanceof Reference) {
                $schema = $components->schemas[$className = $schema->getName()];
            }
            $type = match ($schema->type) {
                'string' => new StringType($schema),
                'integer' => new IntegerType($schema),
                'number' => new NumberType($schema),
                'boolean' => new BooleanType($schema),
                'object' => new ObjectType($schema, $className, $components),
                'array' => new ArrayType($schema, $className, $components),
            };
            $allPossibleRequestBodyPayloadTypes[$type->getNormalizedType()] = $type;
        }

        foreach ($operation->responses->responses as $response) {
            if ($response instanceof Reference) {
                $response = $components->responses[$response->getName()];
            }
            if (count($response->content) === 0) {
                $allPossibleResponseContentTypes['Empty'] = null;
            }
            foreach ($response->content as $type => $mediaType) {
                $allPossibleResponseContentTypes[(string) u($type)->camel()->title()] = $type;
            }
        }

        $cases = [];
        foreach ($allPossibleRequestBodyPayloadTypes as $requestBodyPayloadTypeNormalizedName => $requestBodyPayloadType) {
            foreach ($allPossibleResponseContentTypes as $responseContentTypeNormalizedName => $responseContentType) {
                $cases[] = ActionCase::build(
                    $bundleNamespace,
                    $aggregateName,
                    $actionClassName,
                    $requestBodyPayloadTypeNormalizedName,
                    $requestBodyPayloadType,
                    $responseContentTypeNormalizedName,
                    $responseContentType,
                    $parameters,
                    $operation,
                    $components,
                );
            }
        }

        return $cases;
    }
}