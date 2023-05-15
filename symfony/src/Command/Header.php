<?php

namespace App\Command;

class Header
{
    public readonly Response $response;
    public readonly string $name;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(Response $response, string $name, array $componentsData, array $data): self
    {
        if (isset($data['$ref'])) {
            $data = $componentsData['headers'][explode('/', $data['$ref'])[3]];
        }

        $header = new self();
        $header->response = $response;
        $header->name = $name;
        $header->schema = Schema::build($header, $componentsData, $data['schema']);

        return $header;
    }

    private function __construct()
    {
    }
}