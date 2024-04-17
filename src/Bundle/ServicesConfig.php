<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;
use function Symfony\Component\String\u;

class ServicesConfig implements File
{
    /**
     * @param array<string, Format> $formats
     */
    public static function build(
        string $namespace,
        Api $api,
        array $formats,
    ): self {
        $controllers = [];
        foreach ($api->getAggregates() as $aggregate) {
            $controllers[] = $aggregate->getController();
        }

        $formatValidators = [];
        foreach ($formats as $format) {
            $formatValidators[] = $format->getValidator();
        }

        return new self(
            $namespace,
            $controllers,
            $formatValidators,
        );
    }

    /**
     * @param array<Controller> $controllers
     * @param array<FormatValidator> $formatValidators
     */
    private function __construct(
        private readonly string $namespace,
        private readonly array $controllers,
        private readonly array $formatValidators,
    ) {
    }

    public function getServiceNamespace(): string
    {
        return u($this->namespace)->snake();
    }

    /**
     * @return array<Controller>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @return array<FormatValidator>
     */
    public function getFormatValidators(): array
    {
        return $this->formatValidators;
    }

    public function getFolder(): string
    {
        return 'config';
    }

    public function getName(): string
    {
        return 'services.yaml';
    }

    public function getTemplate(): string
    {
        return 'services.yaml.twig';
    }

    public function getParametersRootName(): string
    {
        return 'services';
    }

    public function hasNamespaceAst(): bool
    {
        return false;
    }

    public function getNamespaceAst(): Namespace_
    {
        throw new \RuntimeException();
    }
}