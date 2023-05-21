<?php

namespace App\Command\OpenApi;

class PathItem
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $route, array $data): self
    {
        return new self(
            $route,
            $parameters = array_map(
                fn (array $data) => isset($data['$ref']) ? Reference::build($data) : Parameter::build($data),
                $data['parameters'] ?? []
            ),
            array_map(
                fn (string $method) => Operation::build($method, $parameters, $data[$method]),
                array_filter(
                    array_keys($data),
                    static fn (string $method) => in_array(
                        $method,
                        ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'],
                        true,
                    ),
                ),
            ),
        );
    }

    private function __construct(
        public readonly string $route,
        /** @var array<Reference|Parameter> */
        public readonly array $parameters,
        /** @var array<Operation> */
        public readonly array $operations,
    ) {
    }
}