<?php

namespace App\Command\OpenApi;

class Parameter
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
    {
        return new self(
            $data['name'],
            $data['in'],
            $data['required'] ?? false,
            isset($data['schema']['$ref']) ?
                Reference::build($data['schema']) : Schema::build($data['schema']),
        );
    }

    private function __construct(
        public readonly string $name,
        public readonly string $in,
        public readonly bool $required,
        public readonly Reference|Schema $schema,
    ) {
    }
}