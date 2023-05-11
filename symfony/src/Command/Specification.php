<?php

namespace App\Command;

class Specification
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
        $files = [];

        foreach ($this->paths as $path) {
            $files = array_merge($files, $path->getFiles());
        }

        return $files;
    }
}