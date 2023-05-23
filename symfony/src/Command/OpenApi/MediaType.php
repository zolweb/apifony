<?php

namespace App\Command\OpenApi;

class MediaType
{
    /**
     * @param array<mixed> $data,
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
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