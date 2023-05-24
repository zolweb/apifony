<?php

namespace App\Command\OpenApi;

class RequestBody
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
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

    private function __construct(
        public readonly bool $required,
        /** @var array<string, MediaType> */
        public readonly array $mediaTypes,
    ) {
    }
}