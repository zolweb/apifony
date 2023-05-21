<?php

namespace App\Command\Bundle;

class File
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public readonly string $folder,
        public readonly string $name,
        public readonly string $template,
        public readonly array $parameters,
    ) {
    }
}