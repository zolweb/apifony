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
            self::buildCases(
                $bundleNamespace,
                $aggregateName,
                $className,
                $operation,
                $components,
                $parameters,
                $requestBodies,
            ),
        );
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionRequestBody> $requestBodies
     * @param array<ActionCase> $cases
     */
    private function __construct(
        private readonly string $name,
        private readonly array $parameters,
        private readonly array $requestBodies,
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
     * @return array<ActionRequestBody>
     *
     * @throws Exception
     */
    private static function buildRequestBodies(
        string $actionClassName,
        Operation $operation,
        Components $components,
    ): array {
        $allPossibleRequestBodies = [];

        $requestBody = $operation->requestBody;
        if ($requestBody instanceof Reference) {
            $requestBody = $components->requestBodies[$requestBody->getName()];
        }

        if ($requestBody === null || !$requestBody->required) {
            $allPossibleRequestBodies[] = ActionRequestBody::build(
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
            $allPossibleRequestBodies[] = ActionRequestBody::build(
                $actionClassName,
                $mimeType,
                $mediaType,
                $components,
            );
        }

        return $allPossibleRequestBodies;
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionRequestBody> $requestBodies
     *
     * @return array<ActionCase>
     */
    private static function buildCases(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Operation $operation,
        Components $components,
        array $parameters,
        array $requestBodies,
    ): array {
        $allPossibleRequestBodyPayloadTypes = [];
        $allPossibleResponseContentTypes = [];

        foreach ($requestBodies as $requestBody) {
            $allPossibleRequestBodyPayloadTypes[$requestBody->getPayloadNormalizedType()] = $requestBody;
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
        foreach ($allPossibleRequestBodyPayloadTypes as $requestBodyPayloadType) {
            foreach ($allPossibleResponseContentTypes as $responseContentTypeNormalizedName => $responseContentType) {
                $cases[] = ActionCase::build(
                    $bundleNamespace,
                    $aggregateName,
                    $actionClassName,
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