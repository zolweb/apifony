<?php

namespace App\Command\OpenApi;

class Header
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        return new self(
            Schema::build($data['schema']),
        );
    }

    private function __construct(
        public readonly Schema $schema,
    ) {
    }
}