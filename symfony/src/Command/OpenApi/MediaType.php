<?php

namespace App\Command\OpenApi;

class MediaType
{
    /**
     * @param array<mixed> $data,
     *
     * @throws Exception
     */
    public static function build(string $type, array $data): self
    {
        return new self(
            $type,
            isset($data['schema']['$ref']) ? Reference::build($data['schema']) : Schema::build($data['schema']),
        );
    }

    private function __construct(
        public readonly string $type,
        public readonly Reference|Schema $schema,
    ) {
    }
}