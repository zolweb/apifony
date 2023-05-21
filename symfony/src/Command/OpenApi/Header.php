<?php

namespace App\Command\OpenApi;

class Header
{
    public readonly string $className;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array $data): self
    {
        $header = new self();
        $header->className = $className;
        $header->schema = Schema::build("{$className}Schema", $data['schema']);

        return $header;
    }

    private function __construct()
    {
    }

    public function addFiles(array& $files, string $folder): void
    {
        $this->schema->addFiles($files, $folder);
    }
}