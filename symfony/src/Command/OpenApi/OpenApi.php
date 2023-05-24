<?php

namespace App\Command\OpenApi;

class OpenApi
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
    {
        if (!is_array($data)) {
            throw new Exception('OpenApi object must be an array.');
        }

        return new self(
            $components = isset($data['components']) ? Components::build($data['components']) : null,
            isset($data['paths']) ? Paths::build($data['paths'], $components) : null,
        );
    }

    private function __construct(
        public readonly ?Components $components,
        public readonly ?Paths $paths,
    ) {
    }
}