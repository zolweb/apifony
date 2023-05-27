<?php

namespace App\Command\OpenApi;

class Header
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
    {
        if (!is_array($data)) {
            throw new Exception('Header object must be an array.');
        }

        return new self(
            match (true) {
                isset($data['schema']) && is_array($data['schema']) && isset($data['schema']['$ref']) => Reference::build($data['schema']),
                isset($data['schema']) => Schema::build($data['schema']),
                default => null,
            },
        );
    }

    private function __construct(
        public readonly null|Reference|Schema $schema,
    ) {
    }
}