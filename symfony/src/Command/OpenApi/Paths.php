<?php

namespace App\Command\OpenApi;

class Paths
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data, Components $components): self
    {
        if (!is_array($data)) {
            throw new Exception('Paths object must be an array.');
        }

        $pathItems = [];
        foreach ($data as $route => $pathItemData) {
            if ($route[0] !== '/') {
                throw new Exception('Paths array keys must start with a slash.');
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