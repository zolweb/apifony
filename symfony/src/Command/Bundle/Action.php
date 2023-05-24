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
            $requestBodies = self::buildRequestBodies($className, $operation, $components),
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

    /**
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
     * @return array<ActionRequestBody>
     *
     * @throws Exception
     */
    private static function buildRequestBodies(
        string $actionClassName,
        Operation $operation,
        Components $components,
    ): array {
        $requestBodies = [];

        $requestBody = $operation->requestBody;
        if ($requestBody instanceof Reference) {
            $requestBody = $components->requestBodies[$requestBody->getName()];
        }

        if ($requestBody === null || !$requestBody->required) {
            $requestBodies[] = ActionRequestBody::build(
                $actionClassName,
                null,
                null,
                $components,
            );
        }

        foreach ($operation->requestBody?->mediaTypes ?? [] as $mimeType => $mediaType) {
            if ($mediaType->schema === null) {
                throw new Exception('MediaTypes without schema are not supported.');
            }
            $requestBodies[] = ActionRequestBody::build(
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
     */
    private static function buildResponseContentTypes(
        Operation $operation,
        Components $components,
    ): array {
        $responseContentTypes = [];

        foreach ($operation->responses->responses as $response) {
            if ($response instanceof Reference) {
                $response = $components->responses[$response->getName()];
            }
            if (count($response->content) === 0) {
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
        Components $components,
        array $parameters,
        array $requestBodyPayloadTypes,
        array $responseContentTypes,
    ): array {
        $cases = [];

        foreach ($requestBodyPayloadTypes as $requestBodyPayloadType) {
            foreach ($responseContentTypes as $responseContentType) {
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

        return $cases;
    }
}