<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Operation;
use App\Command\OpenApi\PathItem;
use function Symfony\Component\String\u;

class Api
{
    public readonly string $folder;
    public readonly string $namespace;
    public readonly string $name;
    /** @var array<Aggregate> */
    public readonly array $aggregates;

    /**
     * @param array<PathItem> $pathItems
     */
    public static function build(
        string $folder,
        string $namespace,
        array $pathItems,
    ): self {
        $api = new self();
        $api->folder = $folder;
        $api->namespace = $namespace;
        $api->aggregates = array_map(
            static fn (array $aggregate) => Aggregate::build(
                "{$folder}/{$aggregate['tag']}",
                "{$namespace}\\{$aggregate['tag']}",
                $aggregate['tag'],
                $aggregate['operations'],
            ),
            array_reduce(
                array_merge(
                    ...array_map(
                    static fn (PathItem $pathItem) => $pathItem->operations,
                    $pathItems,
                ),
                ),
                static function (array $operations, Operation $operation) {
                    $tag = (string) u($operation->tags[0] ?? 'default')->camel()->title();
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
        $files[] = new File(
            $this->folder,
            'AbstractController.php',
            'abstract-controller.php.twig',
            [
                'namespace' => $this->namespace,
            ],
        );

        foreach ($this->aggregates as $aggregate) {
            $aggregate->addFiles($files);
        }
    }
}