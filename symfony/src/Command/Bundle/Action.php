<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class Action
{
    /** @var array<Method> */
    public readonly array $methods;
    public readonly string $name;
    /** @var array<Parameter> */
    public readonly array $parameters;
    public readonly Operation $operation;

    public static function build(
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
                    $parameters,
                    $operation,
                    $components,
                );
            }
        }

        $action = new self();
        $action->name = u($operation->operationId)->camel();
        $action->methods = $methods;
        $action->parameters = $parameters;
        $action->operation = $operation;

        return $action;
    }

    private function __construct()
    {
    }

    public function getParameters(array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_filter(
            $this->parameters,
            static fn (Parameter $param) => in_array($param->parameter->in, $in, true),
        );
    }
}