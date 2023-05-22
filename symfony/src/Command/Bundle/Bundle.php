<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\OpenApi;

class Bundle
{
    public static function build(
        string $namespace,
        OpenApi $openApi,
    ): self {
        return new self(
            Api::build($namespace, $openApi),
        );
    }

    private function __construct(
        private readonly Api $api,
    ) {
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

    /**
     * @return array<Operation>
     */
    public function getAllSortedOperations(): array
    {
        $operations = array_merge(
            ...array_map(
            static fn (PathItem $pathItem) => $pathItem->operations,
            $this->pathItems,
        ),
        );

        usort(
            $operations,
            static fn (Operation $operation1, Operation $operation2) =>
            $operation2->priority - $operation1->priority ?:
                strcmp($operation1->operationId, $operation2->operationId),
        );

        return $operations;
    }
}