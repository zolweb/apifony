<?php

namespace App\Command\OpenApi;

class Paths
{
    /** @var array<PathItem> */
    public readonly array $pathItems;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array& $components, array $data): self
    {
        $paths = new self();
        $paths->pathItems = array_map(
            fn (string $route) => PathItem::build($route, $components, $data[$route]),
            array_filter(
                array_keys($data),
                static fn (string $route) => $route[0] === '/',
            ),
        );

        return $paths;
    }

    private function __construct()
    {
    }

    /**
     * @return array<string>
     */
    public function getAllFirstTags(): array
    {
        return array_unique(
            array_map(
                static fn (Operation $operation) => $operation->tags[0] ?? 'Default',
                array_merge(
                    ...array_map(
                        static fn (PathItem $pathItem) => $pathItem->operations,
                        $this->pathItems,
                    ),
                ),
            ),
        );
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

    public function addFiles(array& $files): void
    {
        foreach ($this->pathItems as $pathItem) {
            $pathItem->addFiles($files);
        }
    }
}