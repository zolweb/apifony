<?php

namespace App\Command\OpenApi;

class Paths
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data, ?Components $components): self
    {
        $pathItems = [];
        foreach ($data as $route => $pathItemData) {
            if (!is_string($route)) {
                throw new Exception('Paths object array keys must be strings.');
            }
            if (!is_array($pathItemData)) {
                throw new Exception('Paths object array elements must be objects.');
            }
            $pathItems[$route] = PathItem::build($pathItemData, $components);
        }

        return new self($pathItems);
    }

    /**
     * @param array<string, PathItem> $pathItems
     */
    private function __construct(
        public readonly array $pathItems,
    ) {
    }
}