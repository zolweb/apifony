<?php

namespace App\Command;

class RequestBody implements Node
{
    public readonly array $mediaTypes;

    public function __construct(
        array $data,
    ) {
        $this->mediaTypes = array_map(
            fn (string $type) => new MediaType($type, $data['content'][$type]),
            array_keys(
                array_filter(
                    $data['content'],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (MediaType $content) => $content->getFiles(),
                $this->mediaTypes,
            ),
        );
    }
}