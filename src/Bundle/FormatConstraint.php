<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\PrettyPrinter\Standard;

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

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $class = $f->class($this->formatName)
            ->extend('Constraint')
            ->addAttribute($f->attribute('\Attribute'))
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Format")
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
