<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Paths
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, ?Components $components, array $path): self
    {
        $pathItems = [];
        $extensions = [];
        foreach ($data as $key => $elementData) {
            if (\is_string($key)) {
                if (str_starts_with($key, '/')) {
                    if (!\is_array($elementData)) {
                        throw new Exception('Paths object array elements must be objects.', $path);
                    }
                    $pathItemPath = $path;
                    $pathItemPath[] = $key;
                    $pathItems[$key] = PathItem::build($elementData, $components, $pathItemPath);
                } elseif (str_starts_with($key, 'x-')) {
                    $extensions[$key] = $elementData;
                }
            }
        }

        return new self($pathItems, $extensions, $path);
    }

    /**
     * @param array<string, PathItem> $pathItems
     * @param array<string, mixed>    $extensions
     * @param list<string>            $path
     */
    private function __construct(
        public readonly array $pathItems,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
