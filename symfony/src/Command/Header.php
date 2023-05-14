<?php

namespace App\Command;

use Exception;

class Header
{
    private readonly Schema $schema;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly Response $response,
        private readonly string $name,
        array $data,
    ) {
        if (in_array($data['schema']['type'] ?? '', ['array', 'object'], true)) {
            throw new Exception("Headers of {$data['schema']['type']} type are not supported yet.");
        }

        $this->schema = Schema::build($this, null, false, $data['schema']);
    }

    public function resolveReference(string $reference): array
    {
        return $this->response->resolveReference($reference);
    }
}