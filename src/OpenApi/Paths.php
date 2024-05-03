<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

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
        $extensions = [];
        foreach ($data as $key => $elementData) {
            if (\is_string($key)) {
                if (str_starts_with($key, '/')) {
                    if (!\is_array($elementData)) {
                        throw new Exception('Paths object array elements must be objects.');
                    }
                    $pathItems[$key] = PathItem::build($elementData, $components);
                } elseif (str_starts_with($key, 'x-')) {
                    $extensions[$key] = $elementData;
                }
            }
        }

        return new self($pathItems, $extensions);
    }

    /**
     * @param array<string, PathItem> $pathItems
     * @param array<string, mixed>    $extensions
     */
    private function __construct(
        public readonly array $pathItems,
        public readonly array $extensions,
    ) {
    }
}
