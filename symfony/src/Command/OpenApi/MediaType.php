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

        return new self(
            match (true) {
                isset($data['schema']['$ref']) => Reference::build($data['schema']),
                isset($data['schema']) => Schema::build($data['schema']),
                default => null,
            }
        );
    }

    private function __construct(
        public readonly null|Reference|Schema $schema,
    ) {
    }
}