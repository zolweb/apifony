<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

class FormatDefinition implements File
{
    public static function build(
        string $bundleNamespace,
        string $formatName,
    ): self {
        return new self(
            $bundleNamespace,
            $formatName,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $formatName,
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Format";
    }

    public function getInterfaceName(): string
    {
        return "{$this->formatName}Definition";
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->formatName}Definition.php";
    }

    public function getTemplate(): string
    {
        return 'format-definition.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'definition';
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