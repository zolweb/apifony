<?php

namespace App\Command;

class Paths
{
    /** @var array<PathItem> */
    public readonly array $pathItems;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array& $components, array $data): self
    {
        $paths = new self();
        $paths->pathItems = array_map(
            fn (string $route) => PathItem::build($route, $components, $data[$route]),
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