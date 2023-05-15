<?php

namespace App\Command;

class PathItem
{
    public readonly Paths $paths;
    public readonly string $route;
    /** @var array<Parameter> */
    public readonly array $parameters;
    public readonly ?Operation $get;
    public readonly ?Operation $put;
    public readonly ?Operation $post;
    public readonly ?Operation $delete;
    public readonly ?Operation $options;
    public readonly ?Operation $head;
    public readonly ?Operation $patch;
    public readonly ?Operation $trace;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(Paths $paths, string $route, array $componentsData, array $data): self
    {
        $pathItem = new self();
        $pathItem->paths = $paths;
        $pathItem->route = $route;
        $pathItem->parameters = array_map(
            fn (array $data) => Parameter::build($pathItem, $componentsData, $data),
            $data['parameters'] ?? []
        );
        $pathItem->get = isset($data['get']) ? Operation::build($pathItem, 'get', $componentsData, $data['get']) : null;
        $pathItem->put = isset($data['put']) ? Operation::build($pathItem, 'put', $componentsData, $data['put']) : null;
        $pathItem->post = isset($data['post']) ? Operation::build($pathItem, 'post', $componentsData, $data['post']) : null;
        $pathItem->delete = isset($data['delete']) ? Operation::build($pathItem, 'delete', $componentsData, $data['delete']) : null;
        $pathItem->options = isset($data['options']) ? Operation::build($pathItem, 'options', $componentsData, $data['options']) : null;
        $pathItem->head = isset($data['head']) ? Operation::build($pathItem, 'head', $componentsData, $data['head']) : null;
        $pathItem->patch = isset($data['patch']) ? Operation::build($pathItem, 'patch', $componentsData, $data['patch']) : null;
        $pathItem->trace = isset($data['trace']) ? Operation::build($pathItem, 'trace', $componentsData, $data['trace']) : null;

        return $pathItem;
    }

    private function __construct()
    {
    }

    public function getFiles(): array
    {
        return array_merge(
            $this->get?->getFiles() ?? [],
            $this->put?->getFiles() ?? [],
            $this->post?->getFiles() ?? [],
            $this->delete?->getFiles() ?? [],
            $this->options?->getFiles() ?? [],
            $this->head?->getFiles() ?? [],
            $this->patch?->getFiles() ?? [],
            $this->trace?->getFiles() ?? [],
        );
    }
}