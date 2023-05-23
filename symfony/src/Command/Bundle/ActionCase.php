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
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        ActionRequestBody $requestBody,
        string $responseContentTypeNormalizedName,
        ?string $responseContentType,
        array $parameters,
        Operation $operation,
        Components $components,
    ): self {
        $responses = [];
        foreach ($operation->responses->responses as $code => $response) {
            if ($response instanceof Reference) {
                $response = $components->responses[$response->getName()];
            }
            if ($responseContentType === null && count($response->content) === 0) {
                $responses[] = ActionResponse::build(
                    $bundleNamespace,
                    $aggregateName,
                    $actionName,
                    $code,
                    'Empty',
                );
            }
            foreach ($response->content as $type => $mediaType) {
                if ($responseContentType === $type) {
                    $responses[] = ActionResponse::build(
                        $bundleNamespace,
                        $aggregateName,
                        $actionName,
                        $code,
                        $type,
                    );
                }
            }
        }

        return new self(
            $requestBody,
            $responseContentTypeNormalizedName,
            $responseContentType,
            sprintf(
                '%sFrom%sPayloadTo%sContent',
                u($operation->operationId)->camel()->title(),
                $requestBody->getPayloadNormalizedType(),
                $responseContentTypeNormalizedName,
            ),
            $parameters,
            $responses,
        );
    }

    private function __construct(
        private readonly ActionRequestBody $requestBody,
        private readonly string $responseContentTypeNormalizedName,
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
        return $this->requestBody->getMimeType() !== null;
    }

    public function getRequestBodyPayloadParameterPhpType(): string
    {
        return $this->requestBody->getPayloadPhpType();
    }

    /**
     * @return array<ActionResponse>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }
}