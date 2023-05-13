<?php

namespace App\Command;

class Path
{
    public readonly array $parameters;
    private readonly array $operations;

    public function __construct(
        private readonly Specification $specification,
        public readonly string $route,
        array $data,
    ) {
        $this->parameters = array_map(
            fn (array $data) => new Parameter($this, $data),
            $data['parameters'] ?? []
        );

        $this->operations = array_map(
            fn (string $method) => new Operation($this, $method, $data[$method]),
            array_keys(
                array_filter(
                    $data,
                    static fn (string $method) => in_array($method, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );
    }

    public function resolveReference(string $reference): array
    {
        return $this->specification->resolveReference($reference);
    }

    public function getFiles(): array
    {
        return array_merge(
                ...array_map(
                static fn (Operation $operation) => $operation->getFiles(),
                $this->operations,
            ),
        );
    }
}