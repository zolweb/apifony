<?php

namespace App\Command;

class Specification
{
    private readonly array $paths;

    public function __construct(array $data)
    {
        $resolveReferences = function (array &$parentNode) use (&$resolveReferences, $data): void {
            foreach ($parentNode as &$childNode) {
                if (is_array($childNode)) {
                    if (isset($childNode['$ref'])) {
                        [, , $type, $name] = explode('/', $childNode['$ref']);
                        $childNode = $data['components'][$type][$name];
                        $childNode['x-ref'] = ['name' => $name];
                    }

                    $resolveReferences($childNode);
                }
            }
        };

        $resolveReferences($data);

        $this->paths = array_map(
            fn (string $route) => new Path($route, $data['paths'][$route]),
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