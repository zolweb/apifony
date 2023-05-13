<?php

namespace App\Command;

class RequestBody
{
    public readonly array $mediaTypes;

    public function __construct(
        private readonly Operation $operation,
        array $data,
    ) {
        $this->mediaTypes = array_map(
            fn (string $type) => new MediaType($this, $type, $data['content'][$type]),
            array_keys(
                array_filter(
                    $data['content'],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );
    }

    public function getClassName(): string
    {
        return "{$this->operation->getNormalizedName()}Request";
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (MediaType $mediaType) => $mediaType->getFiles(),
                $this->mediaTypes,
            ),
        );
    }

    public function resolveReference(string $reference): array
    {
        return $this->operation->resolveReference($reference);
    }
}