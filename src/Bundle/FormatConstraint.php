<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
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
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $class = $f->class($this->formatName)
            ->extend('Constraint')
            ->addAttribute($f->attribute('\Attribute'));

        $namespace = $f->namespace("{$this->bundleNamespace}\Format")
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($class);

        return $namespace->getNode();
    }
}