<?php

namespace App\Command\OpenApi;

class Reference
{
    /**
     * @param array<mixed> $data
     */
    public static function build(array $data): self
    {
        return new self($data['$ref']);
    }

    private function __construct(
        public readonly string $ref,
    ) {
    }

    public function getName(): string
    {
        return explode('/', $this->ref)[3];
    }
}