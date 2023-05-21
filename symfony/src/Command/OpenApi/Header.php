<?php

namespace App\Command\OpenApi;

class Header
{
    public readonly string $className;
    public readonly string $name;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, string $name, array& $components, array $data): self
    {
        $header = new self();

        if (isset($data['$ref'])) {
            $className = explode('/', $data['$ref'])[3];
            $component = &$components['headers'][$className];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $header;
                $data = $component['data'];
            }
        }

        $header->className = $className;
        $header->name = $name;
        $header->schema = Schema::build("{$className}Schema", $components, $data['schema']);

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