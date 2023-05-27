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
        if (!is_array($data)) {
            throw new Exception('Paths object must be an array.');
        }

        $pathItems = [];
        foreach ($data as $route => $pathItemData) {
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