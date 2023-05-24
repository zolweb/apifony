<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\OpenApi;

class Api
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        OpenApi $openApi,
    ): self {
        $aggregates = [];
        foreach ($openApi->paths?->pathItems ?? [] as $route => $pathItem) {
            foreach ($pathItem->operations as $method => $operation) {
                $aggregates[$operation->tags[0] ?? 'default'][] = [
                    'route' => $route,
                    'method' => $method,
                    'operation' => $operation,
                ];
            }
        }

        return new self(
            AbstractController::build($bundleNamespace),
            array_map(
                static fn (string $tag) => Aggregate::build(
                    $bundleNamespace,
                    $tag,
                    $aggregates[$tag],
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
     * @return array<Controller>
     */
    public function getAggregates(): array
    {
        return $this->aggregates;
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