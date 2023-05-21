<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use App\Command\OpenApi\PathItem;
use function Symfony\Component\String\u;

class Api
{
    private readonly string $folder;
    private readonly string $bundleNamespace;
    private readonly string $name;
    private readonly AbstractController $abstractController;
    /** @var array<Aggregate> */
    private readonly array $aggregates;

    /**
     * @param array<PathItem> $pathItems
     */
    public static function build(
        string $bundleNamespace,
        array $pathItems,
        Components $components,
    ): self {
        $api = new self();
        $api->bundleNamespace = $bundleNamespace;
        $api->abstractController = AbstractController::build($bundleNamespace);
        $api->aggregates = array_map(
            static fn (array $aggregate) => Aggregate::build(
                $bundleNamespace,
                u($aggregate['tag'])->camel()->title(),
                $aggregate['operations'],
                $api->abstractController,
                $components,
            ),
            array_reduce(
                array_merge(
                    ...array_map(
                    static fn (PathItem $pathItem) => $pathItem->operations,
                    $pathItems,
                ),
                ),
                static function (array $operations, Operation $operation) {
                    $tag = $operation->tags[0] ?? 'default';
                    if (!isset($operations[$tag])) {
                        $operations[$tag] = ['tag' => $tag, 'operations' => []];
                    }
                    $operations[$tag]['operations'][] = $operation;
                    return $operations;
                },
                [],
            ),
        );

        return $api;
    }

    private function __construct()
    {
    }

    /**
     * @param array<File> $files
     */
    public function addFiles(array& $files): void
    {
        $files[] = $this->abstractController;

        foreach ($this->aggregates as $aggregate) {
            $aggregate->addFiles($files);
        }
    }
}