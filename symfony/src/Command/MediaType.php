<?php

namespace App\Command;

class MediaType
{
    public readonly string $type;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $components,
     * @param array<mixed> $data,
     *
     * @throws Exception
     */
    public static function build(string $className, string $type, array& $components, array $data): self
    {
        $mediaType = new self();
        $mediaType->type = $type;
        $mediaType->schema = Schema::build("{$className}Schema", $components, $data['schema']);

        return $mediaType;
    }

    private function __construct()
    {
    }

    public function addFiles(array& $files): void
    {
        $this->schema->addFiles($files);
    }
}