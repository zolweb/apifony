<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class Action
{
    /** @var array<Method>
     */
    public readonly array $methods;

    public static function build(
        Operation $operation,
        Components $components,
    ): self {
        $requestBodyPayloadTypes = [];
        if ($operation->requestBody === null || !$operation->requestBody->required) {
            $requestBodyPayloadTypes['Empty'] = null;
        }
        foreach ($operation->requestBody?->mediaTypes ?? [] as $mediaType) {
            $name = 'Lol';
            $schema = $mediaType->schema;
            if ($schema instanceof Reference) {
                $schema = $components->schemas[$name = $schema->getName()];
            }
            $type = match ($schema->type) {
                'string' => new StringType($schema),
                'integer' => new IntegerType($schema),
                'number' => new NumberType($schema),
                'boolean' => new BooleanType($schema),
                'object' => new ObjectType($schema, $name),
                'array' => new ArrayType($schema, $components),
            };
            $requestBodyPayloadTypes[$type->getNormalizedType()] = $type;
        }

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

        $methods = [];
        foreach ($requestBodyPayloadTypes as $requestBodyPayloadTypeNormalizedName => $requestBodyPayloadType) {
            foreach ($responseContentTypes as $responseContentTypeNormalizedName => $responseContentType) {
                $methods[] = Method::build(
                    $requestBodyPayloadTypeNormalizedName,
                    $requestBodyPayloadType,
                    $responseContentTypeNormalizedName,
                    $responseContentType,
                    $operation,
                    $components,
                );
            }
        }

        $action = new self();
        $action->methods = $methods;

        return $action;
    }

    private function __construct()
    {
    }
}