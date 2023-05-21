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
        $aggregates = [];
        foreach ($pathItems as $pathItem) {
            foreach ($pathItem->operations as $operation) {
                $tag = $operation->tags[0] ?? 'default';
                if (!isset($aggregates[$tag])) {
                    $aggregates[$tag] = [];
                }
                $aggregates[$tag][] = $operation;
            }
        }

        $api = new self();
        $api->bundleNamespace = $bundleNamespace;
        $api->abstractController = AbstractController::build($bundleNamespace);
        $api->aggregates = array_map(
            static fn (string $tag) => Aggregate::build(
                $bundleNamespace,
                u($tag)->camel()->title(),
                $aggregates[$tag],
                $api->abstractController,
                $components,
            ),
            array_keys($aggregates),
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