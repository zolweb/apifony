<?php

namespace App\Command;

class Specification implements Node
{
    private readonly array $paths;

    public function __construct(array $data)
    {
        $this->paths = array_map(
            fn (string $route) => new Path($this, $route, $data['paths'][$route]),
            array_keys(
                array_filter(
                    $data['paths'],
                    static fn (string $route) => $route[0] === '/',
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (Path $path) => $path->getFiles(),
                $this->paths,
            ),
        );
    }
}