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
        $aggregatesData = [];
        foreach ($openApi->paths->pathItems ?? [] as $route => $pathItem) {
            foreach ($pathItem->operations as $method => $operation) {
                $aggregatesData[$operation->tags[0] ?? 'default'][] = [
                    'route' => $route,
                    'method' => $method,
                    'operation' => $operation,
                ];
            }
        }

        $aggregates = [];
        foreach ($aggregatesData as $tag => $aggregateData) {
            $aggregates[] = Aggregate::build(
                $bundleNamespace,
                $tag,
                $aggregateData,
                $openApi->components,
            );
        }

        return new self(
            AbstractController::build($bundleNamespace),
            $aggregates,
            new DenormalizationException($bundleNamespace),
        );
    }

    private function __construct(
        private readonly AbstractController $abstractController,
        /** @var array<Aggregate> */
        private readonly array $aggregates,
        private readonly DenormalizationException $denormalizationException,
    ) {
    }

    /**
     * @return array<Aggregate>
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
        $files = [
            $this->abstractController,
            $this->denormalizationException,
        ];

        foreach ($this->aggregates as $aggregate) {
            foreach ($aggregate->getFiles() as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }
}