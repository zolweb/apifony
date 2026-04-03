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
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $bundleName,
        OpenApi $openApi,
    ): self {
        $aggregates = [];
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

                $aggregates[] = Aggregate::build(
                    $bundleNamespace,
                    $bundleName,
                    $route,
                    $method,
                    $operation,
                    $openApi->components,
                );
            }
        }

        return new self(
            new AbstractController($bundleNamespace),
            $aggregates,
            new DenormalizationException($bundleNamespace),
            new ParameterValidationException($bundleNamespace),
            new RequestBodyValidationException($bundleNamespace),
        );
    }

    private function __construct(
        private readonly AbstractController $abstractController,
        /** @var list<Aggregate> */
        private readonly array $aggregates,
        private readonly DenormalizationException $denormalizationException,
        private readonly ParameterValidationException $parameterValidationException,
        private readonly RequestBodyValidationException $requestBodyValidationException,
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
            $this->parameterValidationException,
            $this->requestBodyValidationException,
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
