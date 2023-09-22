<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Operation;
use Zol\Ogen\OpenApi\Reference;
use function Symfony\Component\String\u;

class Action
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $route,
        string $method,
        Operation $operation,
        ?Components $components,
    ): self {
        return new self(
            $className = u($operation->operationId)->camel(),
            $route,
            $method,
            $parameters = self::buildParameters($className, $operation, $components),
            $requestBodies = self::buildRequestBodies($bundleNamespace, $aggregateName, $className, $operation, $components),
            $requestBodyPayloadTypes = self::buildRequestBodyPayloadTypes($requestBodies),
            $responseContentTypes = self::buildResponseContentTypes($operation, $components),
            self::buildCases(
                $bundleNamespace,
                $aggregateName,
                $className,
                $operation,
                $components,
                $parameters,
                $requestBodyPayloadTypes,
                $responseContentTypes,
            ),
        );
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionRequestBody> $requestBodies
     * @param array<?Type> $requestBodyPayloadTypes
     * @param array<?string> $responseContentTypes
     * @param array<ActionCase> $cases
     */
    private function __construct(
        private readonly string $name,
        private readonly string $route,
        private readonly string $method,
        private readonly array $parameters,
        private readonly array $requestBodies,
        private readonly array $requestBodyPayloadTypes,
        private readonly array $responseContentTypes,
        private readonly array $cases,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getServiceName(): string
    {
        return u($this->name)->snake();
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param array<string> $in
     *
     * @return array<ActionParameter>
     */
    public function getParameters(array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_filter(
            $this->parameters,
            static fn (ActionParameter $param) => in_array($param->getIn(), $in, true),
        );
    }

    /**
     * @return array<ActionRequestBody>
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @return array<?Type>
     */
    public function getRequestBodyPayloadTypes(): array
    {
        return $this->requestBodyPayloadTypes;
    }

    /**
     * @return array<?string>
     */
    public function getResponseContentTypes(): array
    {
        return $this->responseContentTypes;
    }

    /**
     * @return array<ActionCase>
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    public function getCase(?Type $requestBodyPayloadType, ?string $responseContentType): ActionCase
    {
        return array_values(
            array_filter(
                $this->cases, static fn (ActionCase $case) =>
                    $case->getRequestBodyPayloadType() === $requestBodyPayloadType &&
                    $case->getResponseContentType() === $responseContentType,
            ),
        )[0];
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->cases as $case) {
            foreach ($case->getResponses() as $response) {
                $files[] = $response;
            }
        }
        foreach ($this->requestBodies as $requestBody) {
            foreach ($requestBody->getPayloadModels() as $payloadModel) {
                $files[] = $payloadModel;
            }
        }
        foreach ($this->cases as $case) {
            foreach ($case->getResponses() as $response) {
                foreach ($response->getPayloadModels() as $payloadModel) {
                    $files[] = $payloadModel;
                }
            }
        }

        return $files;
    }

    /**
     * @return array<ActionParameter>
     *
     * @throws Exception
     */
    private static function buildParameters(
        string $actionClassName,
        Operation $operation,
        ?Components $components,
    ): array {
        $ordinal = 0;
        $ordinals = [];
        $parameters = [];
        foreach ($operation->parameters as $parameter) {
            if ($parameter instanceof Reference) {
                if ($components === null || !isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('Reference not found in parameters components.');
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $ordinals[$parameter->name] = ++$ordinal;
            $parameters[] = ActionParameter::build($actionClassName, $parameter, $components);
        }

        usort(
            $parameters,
            static function (ActionParameter $param1, ActionParameter $param2) use ($ordinals) {
                $diff = (int)$param1->hasDefault() - (int)$param2->hasDefault();
                return $diff !== 0 ? $diff : $ordinals[$param1->getRawName()] - $ordinals[$param2->getRawName()];
            }
        );

        return $parameters;
    }

    /**
     * @return array<ActionRequestBody>
     *
     * @throws Exception
     */
    private static function buildRequestBodies(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        ?Components $components,
    ): array {
        $requestBodies = [];

        $requestBody = $operation->requestBody;
        if ($requestBody instanceof Reference) {
            if ($components === null || !isset($components->requestBodies[$requestBody->getName()])) {
                throw new Exception('Reference not found in requestBodies components.');
            }
            $requestBody = $components->requestBodies[$requestBody->getName()];
        }

        // Due to StopLight not allowing to declare required on requestBodies, a requestBody is always required
        if ($requestBody === null || count($requestBody->content) === 0 /* || !$requestBody->required */) {
            $requestBodies[] = ActionRequestBody::build(
                $bundleNamespace,
                $aggregateName,
                $actionClassName,
                null,
                null,
                $components,
            );
        }

        foreach ($requestBody->content ?? [] as $mimeType => $mediaType) {
            if ($mediaType->schema === null) {
                throw new Exception('MediaTypes without schema are not supported.');
            }
            $requestBodies[] = ActionRequestBody::build(
                $bundleNamespace,
                $aggregateName,
                $actionClassName,
                $mimeType,
                $mediaType,
                $components,
            );
        }

        return $requestBodies;
    }

    /**
     * @param array<ActionRequestBody> $requestBodies
     *
     * @return array<?Type>
     */
    private static function buildRequestBodyPayloadTypes(array $requestBodies): array
    {
        $requestBodyPayloadTypes = [];

        foreach ($requestBodies as $requestBody) {
            $requestBodyPayloadTypes[$requestBody->getPayloadNormalizedType()] = $requestBody->getPayloadType();
        }

        return array_values($requestBodyPayloadTypes);
    }

    /**
     * @return array<?string>
     *
     * @throws Exception
     */
    private static function buildResponseContentTypes(
        Operation $operation,
    ?Components $components,
    ): array {
        $responseContentTypes = [];

        foreach ($operation->responses->responses ?? [] as $code => $response) {
            if ($response instanceof Reference) {
                if ($components === null || !isset($components->responses[$response->getName()])) {
                    throw new Exception('Reference not found in responses components.');
                }
                $response = $components->responses[$response->getName()];
            }
            if (count($response->content) === 0 && $code >= 200 && $code < 400) {
                $responseContentTypes['Empty'] = null;
            }
            foreach ($response->content as $type => $mediaType) {
                $responseContentTypes[(string) u($type)->camel()->title()] = $type;
            }
        }

        return array_values($responseContentTypes);
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<?Type> $requestBodyPayloadTypes
     * @param array<?string> $responseContentTypes
     *
     * @return array<ActionCase>
     *
     * @throws Exception
     */
    private static function buildCases(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        ?Components $components,
        array $parameters,
        array $requestBodyPayloadTypes,
        array $responseContentTypes,
    ): array {
        $cases = [];

        foreach ($requestBodyPayloadTypes as $requestBodyPayloadType) {
            foreach ($responseContentTypes as $responseContentType) {
                if ($responseContentType !== null) {
                    $cases[] = ActionCase::build(
                        $bundleNamespace,
                        $aggregateName,
                        $actionClassName,
                        $requestBodyPayloadType,
                        $responseContentType,
                        $parameters,
                        $operation,
                        $components,
                    );
                }
            }
        }

        return $cases;
    }
}