<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

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
                $parameterPath[] = (string) $parameterIndex;
                $operationParameters[] = isset($parameterData['$ref']) ?
                    Reference::build($parameterData, $parameterPath) :
                    Parameter::build($parameterData, $parameterPath);
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
        if (isset($data['x-priority']) && !\is_int($data['x-priority'])) {
            throw new Exception('Operation x-priority attribute must be a string.', $path);
        }
        if (isset($data['tags'])) {
            if (!\is_array($data['tags'])) {
                throw new Exception('Operation tags attribute must be an array.', $path);
            }
            foreach ($data['tags'] as $tag) {
                if (!\is_string($tag)) {
                    throw new Exception('Operation tags array values must be strings.', $path);
                }
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $requestBodyPath = $path;
        $requestBodyPath[] = 'requestBody';
        $responsesBodyPath = $path;
        $responsesBodyPath[] = 'responses';

        return new self(
            $data['operationId'],
            $parameters,
            match (true) {
                isset($data['requestBody']) && \is_array($data['requestBody']) && isset($data['requestBody']['$ref']) => Reference::build($data['requestBody'], $responsesBodyPath),
                isset($data['requestBody']) => RequestBody::build($data['requestBody'], $requestBodyPath),
                default => null,
            },
            isset($data['responses']) ? Responses::build($data['responses'], $responsesBodyPath) : null,
            array_values($data['tags'] ?? []),
            $extensions,
            $path,
        );
    }

    /**
     * @param list<Reference|Parameter> $parameters
     * @param list<string>              $tags
     * @param array<string, mixed>      $extensions
     * @param list<string>              $path
     */
    private function __construct(
        public readonly string $operationId,
        public readonly array $parameters,
        public readonly Reference|RequestBody|null $requestBody,
        public readonly ?Responses $responses,
        public readonly array $tags,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
