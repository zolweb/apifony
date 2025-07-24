<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\PrettyPrinter\Standard;

class FormatValidator implements File
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
        return "{$this->bundleNamespace}\\Format";
    }

    public function getClassName(): string
    {
        return "{$this->formatName}Validator";
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->formatName}Validator.php";
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $setFormatDefinitionMethod = $f->method('setFormatDefinition')
            ->makePublic()
            ->addParam($f->param('formatDefinition')->setType("{$this->formatName}Definition"))
            ->setReturnType('void')
            ->addStmt(new Assign($f->propertyFetch($f->var('this'), 'formatDefinition'), $f->var('formatDefinition')))
        ;

        $validateMethod = $f->method('validate')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraint')->setType('Constraint'))
            ->setReturnType('void')
            ->addStmt(new Foreach_($f->methodCall($f->propertyFetch($f->var('this'), 'formatDefinition'), 'validate', [$f->var('value')]), $f->var('violation'), ['stmts' => [
                new Expression($f->methodCall($f->methodCall($f->propertyFetch($f->var('this'), 'context'), 'buildViolation', [$f->var('violation')]), 'addViolation')),
            ]]))
        ;

        $class = $f->class("{$this->formatName}Validator")
            ->extend('ConstraintValidator')
            ->addStmt($f->property('formatDefinition')->setType("{$this->formatName}Definition")->makePrivate())
            ->addStmt($setFormatDefinitionMethod)
            ->addStmt($validateMethod)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Format")
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintValidator'))
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
