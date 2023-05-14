<?php

namespace App\Command;

class MediaType
{
    private readonly Schema $schema;

    /**
     * @throws \Exception
     */
    public function __construct(
        public readonly RequestBody|Response $requestBody,
        public readonly string $type,
        array $data,
    ) {
        $this->schema = Schema::build($this, null, false, $data['schema']);
    }

    public function resolveReference(string $reference): array
    {
        return $this->requestBody->resolveReference($reference);
    }

    public function getClassName(): string
    {
        return "{$this->requestBody->getClassName()}{$this->type}";
    }

    public function getContentInitializationFromRequest(): string
    {
        return $this->schema->getContentInitializationFromRequest();
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return $this->schema->getContentValidationViolationsInitialization();
    }

    public function getNormalizedType(): string
    {
        return $this->schema->getNormalizedType();
    }

    public function getContentTypeChecking(): string
    {
        return $this->schema->getContentTypeChecking();
    }

    public function getFiles(): array
    {
        return $this->schema->getFiles();
    }
}