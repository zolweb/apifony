<?php

namespace App\Command;

class Header
{
    public readonly string $className;
    public readonly string $name;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(
        string $className,
        string $name,
        array $componentsData,
        array $data,
    ): self {
        if (isset($data['$ref'])) {
            $data = $componentsData['headers'][explode('/', $data['$ref'])[3]];
        }

        $header = new self();
        $header->className = $className;
        $header->name = $name;
        $header->schema = Schema::build(
            "{$className}Schema",
            $componentsData,
            $data['schema'],
        );

        return $header;
    }

    private function __construct()
    {
    }
}