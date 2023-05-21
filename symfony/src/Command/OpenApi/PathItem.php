<?php

namespace App\Command\OpenApi;

class PathItem
{
    public readonly string $route;
    /** @var array<Reference|Parameter> */
    public readonly array $parameters;
    /** @var array<Operation> */
    public readonly array $operations;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $route, array $data): self
    {
        $pathItem = new self();

        $pathItem->route = $route;
        $pathItem->parameters = array_map(
            fn (array $data) => isset($data['$ref']) ? Reference::build($data) : Parameter::build('', $data),
            $data['parameters'] ?? []
        );
        $pathItem->operations = array_map(
            fn (string $method) => Operation::build($pathItem, $method, $data[$method]),
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

    public function addFiles(array& $files): void
    {
        foreach ($this->operations as $operation) {
            $operation->addFiles($files);
        }
    }
}