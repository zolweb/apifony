<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\OpenApi;

class Bundle
{
    public readonly string $packageName;
    public readonly string $namespace;
    public readonly string $name;
    public readonly Api $api;

    public static function build(
        string $namespace,
        OpenApi $openApi,
    ): self {
        $bundle = new self();
        $bundle->api = Api::build(
            'src/Api',
            "{$namespace}\Api",
            $openApi->paths->pathItems,
        );

        return $bundle;
    }

    private function __construct()
    {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [];

        $this->api->addFiles($files);

        return $files;

        $files = [
            [
                'folder' => '',
                'name' => 'composer.json',
                'template' => 'composer.json.twig',
                'params' => [
                    'packageName' => $this->packageName,
                ],
            ],
            [
                'folder' => 'src',
                'name' => "{$this->bundleName}Bundle.php",
                'template' => 'bundle.php.twig',
                'params' => [
                    'bundleName' => $this->bundleName,
                    'paths' => $this->openApi->paths,
                ],
            ],
            [
                'folder' => 'config',
                'name' => 'services.yaml',
                'template' => 'services.yaml.twig',
                'params' => [
                    'paths' => $this->openApi->paths,
                ],
            ],
            [
                'folder' => 'config',
                'name' => 'routes.yaml',
                'template' => 'routes.yaml.twig',
                'params' => [
                    'paths' => $this->openApi->paths,
                ],
            ],
        ];

        $this->apiAggregates->addFiles($files);

        return $files;
    }
}