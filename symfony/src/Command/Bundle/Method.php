<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Type;
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

    public static function build(
        string $requestBodyPayloadTypeNormalizedName,
        ?Type $requestBodyPayloadType,
        string $responseContentTypeNormalizedName,
        ?string $responseContentType,
        Operation $operation,
    ): self {
        $parameters = [];
        foreach ($operation->getAllSortedParameters() as $parameter) {
            $parameters[] = Parameter::build($parameter);
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

        return $method;
    }

    private function __construct()
    {
    }
}