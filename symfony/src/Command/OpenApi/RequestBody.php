<?php

namespace App\Command\OpenApi;

class RequestBody
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        return new self(
            $data['required'] ?? false,
            array_map(
                fn (string $type) => MediaType::build(
                    $type,
                    $data['content'][$type],
                ),
                array_keys(
                    array_filter(
                        $data['content'],
                        static fn (string $type) => in_array($type, ['application/json'], true),
                        ARRAY_FILTER_USE_KEY,
                    ),
                ),
            ),
        );
    }

    private function __construct(
        public readonly bool $required,
        /** @var array<MediaType> */
        public readonly array $mediaTypes,
    ) {
    }
}