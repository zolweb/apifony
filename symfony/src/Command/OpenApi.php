<?php

namespace App\Command;

class OpenApi
{
    public readonly Paths $paths;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $openApi = new self();
        $openApi->paths = Paths::build($openApi, $data['components'] ?? [], $data['paths'] ?? []);

        return $openApi;
    }

    private function __construct()
    {
    }

    public function getFiles(): array
    {
        return $this->paths->getFiles();
    }
}