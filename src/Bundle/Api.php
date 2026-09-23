<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Use_;
use Zol\Apifony\OpenApi\OpenApi;

class Api
{
    /**
     * @param list<Model> $componentModels
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $bundleName,
        OpenApi $openApi,
        array $componentModels,
    ): self {
        $aggregates = [];
        $operationIds = [];
        foreach ($openApi->paths->pathItems ?? [] as $route => $pathItem) {
            foreach ($pathItem->operations as $method => $operation) {
                if (\array_key_exists('x-apifony-ignore', $operation->extensions)) {
                    if (!\is_bool($operation->extensions['x-apifony-ignore'])) {
                        throw new Exception('Operation x-apifony-ignore attribute must be a bool.', $operation->path);
                    }
                    if ($operation->extensions['x-apifony-ignore']) {
                        continue;
                    }
                }

                $aggregate = Aggregate::build(
                    $bundleNamespace,
                    $bundleName,
                    $route,
                    $method,
                    $operation,
                    $openApi->components,
                );
                if (isset($operationIds[$aggregate->getName()])) {
                    throw new Exception(\sprintf('Operations \'%s\' and \'%s\' both map to the \'%s\' aggregate.', $operationIds[$aggregate->getName()], $operation->operationId, $aggregate->getName()), $operation->path);
                }
                $operationIds[$aggregate->getName()] = $operation->operationId;
                $aggregates[] = $aggregate;
            }
        }

        $models = $componentModels;
        foreach ($aggregates as $aggregate) {
            foreach ($aggregate->getFiles() as $file) {
                if ($file instanceof Model) {
                    $models[] = $file;
                }
            }
        }

        return new self(
            new AbstractController($bundleNamespace, $aggregates, $models),
            $aggregates,
            new DenormalizationException($bundleNamespace),
            new ValidationException($bundleNamespace),
        );
    }

    private function __construct(
        private readonly AbstractController $abstractController,
        /** @var list<Aggregate> */
        private readonly array $aggregates,
        private readonly DenormalizationException $denormalizationException,
        private readonly ValidationException $validationException,
    ) {
    }

    /**
     * @return list<Aggregate>
     */
    public function getAggregates(): array
    {
        return $this->aggregates;
    }

    /**
     * @return list<File>
     */
    public function getFiles(): array
    {
        $files = [
            $this->abstractController,
            $this->denormalizationException,
            $this->validationException,
        ];

        foreach ($this->aggregates as $aggregate) {
            foreach ($aggregate->getFiles() as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * @return list<Expression>
     */
    public function getAutoconfiguration(): array
    {
        return array_map(
            static fn (Aggregate $aggregate) => $aggregate->getAutoconfiguration(),
            $this->aggregates,
        );
    }

    /**
     * @return list<Case_>
     */
    public function getCases(): array
    {
        return array_map(
            static fn (Aggregate $aggregate) => $aggregate->getCase(),
            $this->aggregates,
        );
    }

    /**
     * @return list<Use_>
     */
    public function getUses(): array
    {
        return array_map(
            static fn (Aggregate $aggregate) => $aggregate->getUse(),
            $this->aggregates,
        );
    }
}
