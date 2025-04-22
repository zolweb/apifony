<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\PrettyPrinter\Standard;

class EmailValidator implements File
{
    public static function build(
        string $bundleNamespace,
    ): self {
        return new self(
            $bundleNamespace,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\\Format";
    }

    public function getClassName(): string
    {
        return 'EmailValidator';
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return 'EmailValidator.php';
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $validateMethod = $f->method('validate')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraint')->setType('Constraint'))
            ->setReturnType('void')
            ->addStmt(new If_($f->funcCall('\is_string', [$f->var('value')]), ['stmts' => [
                new Expression(new Assign($f->var('constraints'), new Array_([new ArrayItem($f->new('NotBlank')), new ArrayItem($f->new('\Symfony\Component\Validator\Constraints\Email', ['mode' => $f->val('strict')]))]))),
                new Expression(new Assign($f->var('violations'), $f->methodCall($f->staticCall('Validation', 'createValidator'), 'validate', [$f->var('value'), $f->var('constraints')]))),
                new Foreach_($f->var('violations'), $f->var('violation'), ['stmts' => [
                    new Expression($f->methodCall($f->methodCall($f->propertyFetch($f->var('this'), 'context'), 'buildViolation', [new String_($f->methodCall($f->var('violation'), 'getMessage'))]), 'addViolation')),
                ]]),
            ]]))
        ;

        $class = $f->class('EmailValidator')
            ->extend('ConstraintValidator')
            ->addStmt($validateMethod)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Format")
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($f->use('Symfony\Component\Validator\Constraints\NotBlank'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintValidator'))
            ->addStmt($f->use('Symfony\Component\Validator\Validation'))
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
