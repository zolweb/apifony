<?php

namespace App\Command;

class Response
{
    private readonly array $headers;
    public readonly array $mediaTypes;

    public function __construct(
        private readonly Operation $operation,
        private readonly int|string $code,
        array $data,
    ) {
        $this->mediaTypes = array_map(
            fn (string $type) => new MediaType($this, $type, $data['content'][$type]),
            array_keys(
                array_filter(
                    $data['content'] ?? [],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );

        $this->headers = array_map(
            fn (string $name) => new Header($this, $name, $data['headers'][$name]),
            array_keys($data['headers'] ?? []),
        );
    }

    public function resolveReference(string $reference): array
    {
        return $this->operation->resolveReference($reference);
    }

    public function getClassName(): string
    {
        return "{$this->operation->getNormalizedName()}Response";
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
}