<?php

namespace App\Command\OpenApi;

class OpenApi
{
    public readonly ?Components $components;
    public readonly ?Paths $paths;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $openApi = new self();
        $openApi->components = isset($data['components']) ? Components::build($data['components']) : null;
        $openApi->paths = isset($data['paths']) ? Paths::build($data['paths']) : null;

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