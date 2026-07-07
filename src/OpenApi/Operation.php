<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Operation
{
    /**
     * @param list<Reference|Parameter> $pathItemParameters
     * @param array<mixed>              $data
     * @param list<string>              $path
     *
     * @throws Exception
     */
    public static function build(array $pathItemParameters, array $data, ?Components $components, array $path): self
    {
        $operationParameters = [];
        if (isset($data['parameters'])) {
            if (!\is_array($data['parameters'])) {
                throw new Exception('Operation parameters must be an array.', $path);
            }
            foreach ($data['parameters'] as $parameterIndex => $parameterData) {
                if (!\is_int($parameterIndex)) {
                    throw new Exception('Parameter indexes must be integers.', $path);
                }
                if (!\is_array($parameterData)) {
                    throw new Exception('Parameter or Reference objects must be arrays.', $path);
                }
                $parameterPath = $path;
                $parameterPath[] = 'parameters';
                $parameterPath[] = (string) $parameterIndex;
                $operationParameters[] = isset($parameterData['$ref'])
                    ? Reference::build($parameterData, $parameterPath)
                    : Parameter::build($parameterData, $parameterPath);
            }
        }

        $indexedPathItemParameters = [];
        foreach ($pathItemParameters as $parameterReference) {
            $parameter = $parameterReference;
            if ($parameter instanceof Reference) {
                if (!isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('All references to parameters must exist in components object parameters.', $path);
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $index = "{$parameter->in}:{$parameter->name}";
            $indexedPathItemParameters[$index] = $parameter;
        }

        $indexedOperationParameters = [];
        foreach ($operationParameters as $parameterReference) {
            $parameter = $parameterReference;
            if ($parameter instanceof Reference) {
                if (!isset($components->parameters[$parameter->getName()])) {
                    throw new Exception('All references to parameters must exist in components object parameters.', $path);
                }
                $parameter = $components->parameters[$parameter->getName()];
            }
            $index = "{$parameter->in}:{$parameter->name}";
            $indexedOperationParameters[$index] = $parameter;
        }

        $parameters = array_values(array_merge($indexedPathItemParameters, $indexedOperationParameters));

        if (!isset($data['operationId'])) {
            throw new Exception('Operation operationId is mandatory.', $path);
        }
        if (!\is_string($data['operationId'])) {
            throw new Exception('Operation operationId attribute must be a string.', $path);
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $requestBody = null;
        $requestBodyRef = null;
        if (isset($data['requestBody'])) {
            if (!\is_array($data['requestBody'])) {
                throw new Exception('Operation object requestBody attribute must be an array.', $path);
            }
            $requestBody = $data['requestBody'];
            if (isset($data['requestBody']['$ref'])) {
                if (!\is_string($data['requestBody']['$ref'])) {
                    throw new Exception('Operation object requestBody attribute $ref attribute must be a string.', $path);
                }
                $requestBodyRef = $data['requestBody']['$ref'];
            }
        }

        $responses = null;
        if (isset($data['responses'])) {
            if (!\is_array($data['responses'])) {
                throw new Exception('Operation object responses attribute must be an array.', $path);
            }
            $responses = $data['responses'];
        }

        $requestBodyPath = $path;
        $requestBodyPath[] = 'requestBody';
        $responsesBodyPath = $path;
        $responsesBodyPath[] = 'responses';

        return new self(
            $data['operationId'],
            $parameters,
            match (true) {
                $requestBody !== null && $requestBodyRef !== null => Reference::build($requestBody, $responsesBodyPath),
                $requestBody !== null => RequestBody::build($requestBody, $requestBodyPath),
                default => null,
            },
            $responses !== null ? Responses::build($responses, $responsesBodyPath) : null,
            $extensions,
            $path,
        );
    }

    /**
     * @param list<Reference|Parameter> $parameters
     * @param array<string, mixed>      $extensions
     * @param list<string>              $path
     */
    private function __construct(
        public readonly string $operationId,
        public readonly array $parameters,
        public readonly Reference|RequestBody|null $requestBody,
        public readonly ?Responses $responses,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
