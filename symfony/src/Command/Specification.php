<?php

namespace App\Command;

class Specification
{
    private readonly array $paths;
    private readonly array $components;

    public function __construct(array $data)
    {
        $this->components = $data['components'] ?? [];

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

    public function resolveReference(string $reference): array
    {
        [, , $type, $name] = explode('/', $reference);

        return [
            'type' => $type,
            'name' => $name,
            'data' => $this->components[$type][$name],
        ];
    }
}