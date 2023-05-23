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
        foreach ($openApi->paths?->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->operations as $operation) {
                $aggregates[$operation->tags[0] ?? 'default'][] = $operation;
            }
        }

        return new self(
            $abstractController = AbstractController::build($bundleNamespace),
            array_map(
                static fn (string $tag) => Aggregate::build(
                    $bundleNamespace,
                    $tag,
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
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [$this->abstractController];

        foreach ($this->aggregates as $aggregate) {
            foreach ($aggregate->getFiles() as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }
}