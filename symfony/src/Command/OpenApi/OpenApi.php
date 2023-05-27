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
        if (isset($data['components']) && !is_array($data['components'])) {
            throw new Exception('OpenApi object components attribute must be an array.');
        }
        if (isset($data['paths']) && !is_array($data['paths'])) {
            throw new Exception('OpenApi object paths attribute must be an array.');
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