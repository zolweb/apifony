<?php

namespace App\Command\OpenApi;

class Reference
{
    public readonly string $ref;

    /**
     * @param array<mixed> $data
     */
    public static function build(array $data): self
    {
        $reference = new self();
        $reference->ref = $data['$ref'];

        return $reference;
    }

    private function __construct()
    {
    }
}