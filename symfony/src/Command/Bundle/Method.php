<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class Method
{
    public readonly string $requestBodyPayloadTypeNormalizedName;
    public readonly ?Type $requestBodyPayloadType;
    public readonly string $responseContentTypeNormalizedName;
    public readonly ?string $responseContentType;
    public readonly string $name;
    /** @var array<Parameter> */
    public readonly array $parameters;
    /** @var array<Responsex> */
    public readonly array $responses;

    public static function build(
        string $requestBodyPayloadTypeNormalizedName,
        ?Type $requestBodyPayloadType,
        string $responseContentTypeNormalizedName,
        ?string $responseContentType,
        Operation $operation,
        Components $components,
    ): self {
        $params = $operation->parameters;
        usort(
            $params,
            static fn (\App\Command\OpenApi\Parameter $param1, \App\Command\OpenApi\Parameter $param2) =>
            ((int)($param1->schema->default !== null) - (int)($param2->schema->default !== null)) ?:
                strcmp($param1->name, $param2->name),
        );

        $parameters = [];
        foreach ($params as $parameter) {
            $parameters[] = Parameter::build($parameter, $components);
        }

        $responses = [];
        foreach ($operation->responses->responses as $code => $response) {
            if ($response instanceof Reference) {
                $response = $components->responses[$response->getName()];
            }
            if ($responseContentType === null && count($response->content) === 0) {
                $responses[] = sprintf(
                    '%s%sEmpty',
                    u($operation->operationId)->camel()->title(),
                    $code,
                );
            }
            foreach ($response->content as $type => $mediaType) {
                if ($responseContentType === $type) {
                    $responses[] = sprintf(
                        '%s%s%s',
                        u($operation->operationId)->camel()->title(),
                        $code,
                        u($type)->camel()->title(),
                    );
                }
            }
        }

        $method = new self();
        $method->requestBodyPayloadTypeNormalizedName = $requestBodyPayloadTypeNormalizedName;
        $method->requestBodyPayloadType = $requestBodyPayloadType;
        $method->responseContentTypeNormalizedName = $responseContentTypeNormalizedName;
        $method->responseContentType = $responseContentType;
        $method->name = sprintf(
            '%sFrom%sPayloadTo%sContent',
            u($operation->operationId)->camel()->title(),
            $requestBodyPayloadTypeNormalizedName,
            $responseContentTypeNormalizedName,
        );
        $method->parameters = $parameters;
        $method->responses = $responses;

        return $method;
    }

    private function __construct()
    {
    }
}