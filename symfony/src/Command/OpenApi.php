<?php

namespace App\Command;

class OpenApi
{
    public readonly ?Paths $paths;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $components = [];
        foreach ($data['components'] ?? [] as $type => $typeComponents) {
            foreach ($typeComponents as $name => $component) {
                $components[$type][$name] = ['data' => $component, 'instance' => null];
            }
        }

        $openApi = new self();
        $openApi->paths = isset($data['paths']) ? Paths::build($components, $data['paths']) : null;

        return $openApi;
    }

    private function __construct()
    {
    }

    public function getFiles(): array
    {
        $files = [];

        $this->paths->addFiles($files);

        return $files;
    }
}