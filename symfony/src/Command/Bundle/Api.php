<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\OpenApi;
use function Symfony\Component\String\u;

class Api
{
    public static function build(
        string $bundleNamespace,
        OpenApi $openApi,
    ): self {
        $aggregates = [];
        foreach ($openApi->paths->pathItems as $pathItem) {
            foreach ($pathItem->operations as $operation) {
                $tag = $operation->tags[0] ?? 'default';
                if (!isset($aggregates[$tag])) {
                    $aggregates[$tag] = [];
                }
                $aggregates[$tag][] = $operation;
            }
        }

        return new self(
            $abstractController = AbstractController::build($bundleNamespace),
            array_map(
                static fn (string $tag) => Aggregate::build(
                    $bundleNamespace,
                    u($tag)->camel()->title(),
                    $aggregates[$tag],
                    $abstractController,
                    $openApi->components,
                ),
                array_keys($aggregates),
            ),
        );
    }

    private function __construct(
        private readonly AbstractController $abstractController,
        /** @var array<Aggregate> */
        private readonly array $aggregates,
    ) {
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