<?php

namespace App\Command;

class MediaType
{
    public readonly RequestBody|Response $parent;
    public readonly string $type;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $componentsData,
     * @param array<mixed> $data,
     *
     * @throws Exception
     */
    public static function build(
        RequestBody|Response $parent,
        string $className,
        string $type,
        array $componentsData,
        array $data,
    ): self {
        $mediaType = new self();
        $mediaType->parent = $parent;
        $mediaType->type = $type;
        $mediaType->schema = Schema::build(
            $mediaType,
            "{$className}Schema",
            $componentsData,
            $data['schema'],
        );

        return $mediaType;
    }

    private function __construct()
    {
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