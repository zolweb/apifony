<?php

namespace App\Command\OpenApi;

class Header
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
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