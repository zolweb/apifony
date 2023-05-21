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
            isset($data['schema']['$ref']) ? Reference::build($data['schema']) : Schema::build($data['schema']),
        );
    }

    private function __construct(
        public readonly Reference|Schema $schema,
    ) {
    }
}