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
            $schema = $mediaType->schema;
            if ($schema instanceof Reference) {
                $schema = $components->schemas[$schema->getName()];
            }
            $type = match ($schema->type) {
                'string' => new StringType($schema),
                'integer' => new IntegerType($schema),
                'number' => new NumberType($schema),
                'boolean' => new BooleanType($schema),
                'object' => new ObjectType($schema),
                'array' => new ArrayType($schema),
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
            foreach ($response->content as $mediaType) {
                $responseContentTypes[(string) u($mediaType->type)->camel()->title()] = $mediaType->type;
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
                );
            }
        }

        $action = new self();
        $action->methods = $methods;


        usort(
            $params,
            static fn (Parameter $param1, Parameter $param2) =>
            ((int)($param1->schema->default !== null) - (int)($param2->schema->default !== null)) ?:
                strcmp($param1->name, $param2->name),
        );

        return $action;
    }

    private function __construct()
    {
    }

    public function getAllPossibleResponsesForContentType(?string $contentType): array
    {
        $responses = [];

        foreach ($this->responses->responses as $response) {
            if ($contentType === null && count($response->content) === 0) {
                $responses[] = "{$response->className}Empty";
            }

            foreach ($response->content as $mediaType) {
                if ($contentType === $mediaType->type) {
                    $responses[] = $mediaType->className;
                }
            }
        }

        return $responses;
    }
}