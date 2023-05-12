<?php

namespace App\Command;

class RequestBody
{
    public readonly array $contents;

    public function __construct(
        array $data,
    ) {
        $this->contents = array_map(
            fn (string $type) => new Content($type, $data['content'][$type]),
            array_keys(
                array_filter(
                    $data['content'],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );
    }
}