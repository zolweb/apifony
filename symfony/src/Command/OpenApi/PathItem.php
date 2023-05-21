<?php

namespace App\Command\OpenApi;

class PathItem
{
    public readonly string $route;
    /** @var array<Parameter> */
    public readonly array $parameters;
    /** @var array<Operation> */
    public readonly array $operations;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $route, array& $components, array $data): self
    {
        $pathItem = new self();

        if (isset($data['$ref'])) {
            $component = &$components['pathItems'][explode('/', $data['$ref'])[3]];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $pathItem;
                $data = $component['data'];
            }
        }

        $pathItem->route = $route;
        $pathItem->parameters = array_map(
            fn (array $data) => Parameter::build('', $components, $data),
            $data['parameters'] ?? []
        );
        $pathItem->operations = array_map(
            fn (string $method) => Operation::build($pathItem, $method, $components, $data[$method]),
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