<?php

namespace App\Command;

class MediaType implements Node
{
    private readonly Schema $schema;

    public function __construct(
        public Node $parent,
        public readonly string $type,
        array $data,
    ) {
        $this->schema = new Schema($this, false, $data['schema']);
    }

    public function getFiles(): array
    {
        return $this->schema->getFiles();
    }

    public function resolveReference(string $reference): array
    {
        return $this->parent->resolveReference($reference);
    }
}