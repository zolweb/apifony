<?php

namespace App\Command;

class MediaType
{
    private readonly Schema $schema;

    public function __construct(
        public readonly RequestBody $requestBody,
        public readonly string $type,
        array $data,
    ) {
        $this->schema = new Schema($this, null, false, $data['schema']);
    }

    public function getClassName(): string
    {
        return "{$this->requestBody->getClassName()}{$this->type}";
    }

    public function getFiles(): array
    {
        return $this->schema->getFiles();
    }

    public function resolveReference(string $reference): array
    {
        return $this->requestBody->resolveReference($reference);
    }
}