<?php

namespace App\Command\OpenApi;

use function Symfony\Component\String\u;

class MediaType
{
    public readonly string $className;
    public readonly string $type;
    public readonly Reference|Schema $schema;

    /**
     * @param array<mixed> $data,
     *
     * @throws Exception
     */
    public static function build(string $className, string $type, array $data): self
    {
        $mediaType = new self();
        $mediaType->className = $className;
        $mediaType->type = $type;
        $mediaType->schema = isset($data['schema']['$ref']) ?
            Reference::build($data['schema']) : Schema::build("{$className}Schema", $data['schema']);

        return $mediaType;
    }

    private function __construct()
    {
    }

    public function getNormalizedType(): string
    {
        return u($this->type)->camel()->title();
    }

    public function addFiles(array& $files, string $folder): void
    {
        $this->schema->addFiles($files, $folder);
    }
}