<?php

namespace App\Command\OpenApi;

class OpenApi
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        return new self(
            isset($data['components']) ? Components::build($data['components']) : null,
            isset($data['paths']) ? Paths::build($data['paths']) : null,
        );
    }

    private function __construct(
        public readonly ?Components $components,
        public readonly ?Paths $paths,
    ) {
    }
}