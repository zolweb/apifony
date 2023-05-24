<?php

namespace App\Command\OpenApi;

class Operation
{
    /**
     * @param array<Reference|Parameter> $pathItemParameters
     *
     * @throws Exception
     */
    public static function build(array $pathItemParameters, mixed $data): self
    {
        $operationParameters = array_map(
            fn (array $parameterData) => isset($parameterData['$ref']) ?
                Reference::build($parameterData) : Parameter::build($parameterData),
            $data['parameters'] ?? []
        );

        $pathItemParameters = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $pathItemParameters),
            $pathItemParameters,
        );

        $operationParameters = array_combine(
            array_map(fn (Parameter $param) => "{$param->in}:{$param->name}", $operationParameters),
            $operationParameters,
        );

        $parameters = array_values(array_merge($pathItemParameters, $operationParameters));

        return new self(
            $data['operationId'],
            $data['x-priority'] ?? 0,
            $parameters,
            match (true) {
                isset($data['requestBody']['$ref']) => Reference::build($data['requestBody']),
                isset($data['requestBody']) => RequestBody::build($data['requestBody']),
                default => null,
            },
            isset($data['responses']) ?
                Responses::build($data['responses']) : null,
            $data['tags'] ?? [],
        );
    }

    private function __construct(
        public readonly string $operationId,
        public readonly int $priority,
        /** @var array<Reference|Parameter> */
        public readonly array $parameters,
        public readonly null|Reference|RequestBody $requestBody,
        public readonly ?Responses $responses,
        /** @var array<string> */
        public readonly array $tags,
    ) {
    }
}