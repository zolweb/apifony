<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

class FormatConstraint implements File
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

    public function getClassName(): string
    {
        return $this->formatName;
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->formatName}.php";
    }

    public function getTemplate(): string
    {
        return 'format-constraint.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'constraint';
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