<?php

namespace App\Command;

class PathItem
{
    public readonly string $route;
    /** @var array<Parameter> */
    public readonly array $parameters;
    /** @var array<Operation> */
    public readonly array $operations;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $route, array $componentsData, array $data): self
    {
        if (isset($data['$ref'])) {
            $data = $componentsData['pathItems'][explode('/', $data['$ref'])[3]];
        }

        $pathItem = new self();
        $pathItem->route = $route;
        $pathItem->parameters = array_map(
            fn (array $data) => Parameter::build('', $componentsData, $data),
            $data['parameters'] ?? []
        );
        $pathItem->operations = array_map(
            fn (string $method) => Operation::build($pathItem, $method, $componentsData, $data[$method]),
            array_filter(
                array_keys($data),
                static fn (string $method) => in_array($method, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true),
            ),
        );

        return $pathItem;
    }

    private function __construct()
    {
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