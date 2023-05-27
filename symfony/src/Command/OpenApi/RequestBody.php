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
            array_combine(
                $types = array_keys(
                    array_filter(
                        $data['content'],
                        static fn (string $type) => in_array($type, ['application/json'], true),
                        ARRAY_FILTER_USE_KEY,
                    ),
                ),
                array_map(
                    fn (string $type) => MediaType::build(
                        $data['content'][$type],
                    ),
                    $types,
                ),
            )
        );
    }

    /**
     * @param array<string, MediaType> $mediaTypes
     */
    private function __construct(
        public readonly bool $required,
        public readonly array $mediaTypes,
    ) {
    }
}