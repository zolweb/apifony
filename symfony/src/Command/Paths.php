<?php

namespace App\Command;

class Paths
{
    public readonly OpenApi $openApi;
    /** @var array<PathItem> */
    public readonly array $pathItems;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(OpenApi $openApi, array $componentsData, array $data): self
    {
        $paths = new self();
        $paths->openApi = $openApi;
        $paths->pathItems = array_map(
            fn (string $route) => PathItem::build($paths, $route, $componentsData, $data[$route]),
            array_filter(
                array_keys($data),
                static fn (string $route) => $route[0] === '/',
            ),
        );

        return $paths;
    }

    private function __construct()
    {
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (PathItem $pathItem) => $pathItem->getFiles(),
                $this->pathItems,
            ),
        );
    }
}