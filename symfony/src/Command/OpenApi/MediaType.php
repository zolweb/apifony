<?php

namespace App\Command\OpenApi;

class MediaType
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        if (isset($data['schema']) && !is_array($data['schema'])) {
            throw new Exception('MediaType object schema attribute must be an array.');
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self(
            match (true) {
                isset($data['schema']['$ref']) => Reference::build($data['schema']),
                isset($data['schema']) => Schema::build($data['schema']),
                default => null,
            },
            $extensions,
        );
    }

    /**
     * @param array<mixed> $extensions
     */
    private function __construct(
        public readonly null|Reference|Schema $schema,
        public readonly array $extensions,
    ) {
    }
}