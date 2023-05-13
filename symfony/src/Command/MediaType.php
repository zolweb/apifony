<?php

namespace App\Command;

class MediaType
{
    private readonly Schema $schema;

    public function __construct(
        public readonly string $type,
        array $data,
    ) {
        $this->schema = new Schema($this, null, false, $data['schema']);
    }

    public function getFiles(): array
    {
        return $this->schema->getFiles();
    }
}