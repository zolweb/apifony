<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class ActionCase
{
    /**
     * @param array<ActionParameter> $parameters
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        ?Type $requestBodyPayloadType,
        ?string $responseContentType,
        array $parameters,
        Operation $operation,
        ?Components $components,
    ): self {
        $responses = [];
        foreach ($operation->responses->responses ?? [] as $code => $response) {
            if ($response instanceof Reference) {
                if ($components === null || !isset($components->responses[$response->getName()])) {
                    throw new Exception('Reference not found in responses components.');
                }
                $response = $components->responses[$response->getName()];
            }
            if ($responseContentType === null && count($response->content) === 0) {
                $responses[] = ActionResponse::build(
                    $bundleNamespace,
                    $aggregateName,
                    $actionName,
                    $code,
                    $response,
                    null,
                    null,
                    $components,
                );
            }
            foreach ($response->content as $type => $mediaType) {
                if ($responseContentType === $type) {
                    $responses[] = ActionResponse::build(
                        $bundleNamespace,
                        $aggregateName,
                        $actionName,
                        $code,
                        $response,
                        $type,
                        $mediaType->schema,
                        $components,
                    );
                }
            }
        }

        return new self(
            $requestBodyPayloadType,
            $responseContentType,
            sprintf(
                '%sFrom%sPayloadTo%sContent',
                u($operation->operationId)->camel()->title(),
                $requestBodyPayloadType?->getNormalizedType() ?? 'Empty',
                u($responseContentType)->camel()->title(),
            ),
            $parameters,
            $responses,
        );
    }

    /**
     * @param array<ActionParameter> $parameters
     * @param array<ActionResponse> $responses
     */
    private function __construct(
        private readonly ?Type $requestBodyPayloadType,
        private readonly ?string $responseContentType,
        private readonly string $name,
        private readonly array $parameters,
        private readonly array $responses,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<ActionParameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function hasRequestBodyPayloadParameter(): bool
    {
        return $this->requestBodyPayloadType !== null;
    }

    public function getRequestBodyPayloadType(): ?Type
    {
        return $this->requestBodyPayloadType;
    }

    public function getRequestBodyPayloadParameterPhpType(): ?string
    {
        return $this->requestBodyPayloadType?->getMethodParameterType();
    }

    public function getResponseContentType(): ?string
    {
        return $this->responseContentType;
    }

    /**
     * @return array<ActionResponse>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }
}