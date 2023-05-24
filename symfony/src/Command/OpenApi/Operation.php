<?php

namespace App\Command\OpenApi;

class Operation
{
    /**
     * @param array<Reference|Parameter> $pathItemParameters
     *
     * @throws Exception
     */
    public static function build(array $pathItemParameters, mixed $data, ?Components $components): self
    {
        if (!is_array($data)) {
            throw new Exception('Operation objects must be arrays.');
        }

        $operationParameters = [];
        if (isset($data['parameters'])) {
            if (!is_array($data['parameters'])) {
                throw new Exception('Operation parameters must be an array.');
            }
            foreach ($data['parameters'] as $parameterData) {
                if (!is_array($parameterData)) {
                    throw new Exception('Parameter or Reference objects must be arrays.');
                }
                $operationParameters[] = isset($parameterData['$ref']) ?
                    Reference::build($parameterData) :
                    Parameter::build($parameterData);
            }
        }

        $indexedPathItemParameters = [];
        foreach ($pathItemParameters as $parameterReference) {
            $parameter = $parameterReference;
            if ($parameter instanceof Reference) {
                if (!isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('All references to parameters must exist in components object parameters.');
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $index = "{$parameter->in}:{$parameter->name}";
            if (isset($indexedPathItemParameters[$index])) {
                throw new Exception('PathItem parameters must be unique by \'in\' and \'name\'.');
            }
            $indexedPathItemParameters[$index] = $parameter;
        }

        $indexedOperationParameters = [];
        foreach ($operationParameters as $parameterReference) {
            $parameter = $parameterReference;
            if ($parameter instanceof Reference) {
                if (!isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('All references to parameters must exist in components object parameters.');
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $index = "{$parameter->in}:{$parameter->name}";
            if (isset($indexedOperationParameters[$index])) {
                throw new Exception('PathItem parameters must be unique by \'in\' and \'name\'.');
            }
            $indexedOperationParameters[$index] = $parameter;
        }

        $parameters = array_values(array_merge($indexedPathItemParameters, $indexedOperationParameters));

        if (!isset($data['operationId'])){
            throw new Exception('Operation operationId is mandatory.');
        }
        if (!is_string($data['operationId'])) {
            throw new Exception('Operation operationId attribute must be a string.');
        }
        if (isset($data['x-priority']) && !is_int($data['x-priority'])) {
            throw new Exception('Operation x-priority attribute must be a string.');
        }
        if (isset($data['tags'])) {
            if (!is_array($data['tags'])) {
                throw new Exception('Operation tags attribute must be an array.');
            }
            foreach ($data['tags'] as $tag) {
                if (!is_string($tag)) {
                    throw new Exception('Operation tags array values must be strings.');
                }
            }
        }

        return new self(
            $data['operationId'],
            $data['x-priority'] ?? 0, // TODO Not stantard openapi, save all custom attributes instead
            $parameters,
            match (true) {
                isset($data['requestBody']['$ref']) => Reference::build($data['requestBody']),
                isset($data['requestBody']) => RequestBody::build($data['requestBody']),
                default => null,
            },
            isset($data['responses']) ? Responses::build($data['responses']) : null,
            array_values($data['tags'] ?? []),
        );
    }

    /**
     * @param array<Reference|Parameter> $parameters
     * @param array<string> $tags
     */
    private function __construct(
        public readonly string $operationId,
        public readonly int $priority,
        public readonly array $parameters,
        public readonly null|Reference|RequestBody $requestBody,
        public readonly ?Responses $responses,
        public readonly array $tags,
    ) {
    }
}