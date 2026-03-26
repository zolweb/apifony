<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;

class DateTimeValidator implements File
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
        return 'DateTimeValidator';
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return 'DateTimeValidator.php';
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
                new Expression(new Assign($f->var('normalizedValue'), $f->funcCall('str_replace', [new Array_([new ArrayItem($f->val('t')), new ArrayItem($f->val('z'))], ['kind' => Array_::KIND_SHORT]), new Array_([new ArrayItem($f->val('T')), new ArrayItem($f->val('Z'))], ['kind' => Array_::KIND_SHORT]), $f->var('value')]))),
                new Expression(new Assign($f->var('allowedFormats'), new Array_([new ArrayItem($f->val('!Y-m-d\TH:i:sP')), new ArrayItem($f->val('!Y-m-d\TH:i:s\Z')), new ArrayItem($f->val('!Y-m-d\TH:i:s.uP')), new ArrayItem($f->val('!Y-m-d\TH:i:s.u\Z'))], ['kind' => Array_::KIND_SHORT]))),
                new Foreach_($f->var('allowedFormats'), $f->var('allowedFormat'), ['stmts' => [
                    new Expression(new Assign($f->var('dateTime'), $f->staticCall('\DateTimeImmutable', 'createFromFormat', [$f->var('allowedFormat'), $f->var('normalizedValue')]))),
                    new Expression(new Assign($f->var('errors'), $f->staticCall('\DateTimeImmutable', 'getLastErrors'))),
                    new If_(new BooleanAnd(new NotIdentical($f->var('dateTime'), $f->val(false)), new BooleanOr(new Identical($f->var('errors'), $f->val(false)), new BooleanAnd(new Identical(new ArrayDimFetch($f->var('errors'), $f->val('warning_count')), $f->val(0)), new Identical(new ArrayDimFetch($f->var('errors'), $f->val('error_count')), $f->val(0))))), ['stmts' => [
                        new Return_(),
                    ]]),
                ]]),
                new Expression($f->methodCall($f->methodCall($f->propertyFetch($f->var('this'), 'context'), 'buildViolation', [$f->val('This is not a valid date time format according to RFC 3339.')]), 'addViolation')),
            ]]))
        ;

        $class = $f->class('DateTimeValidator')
            ->extend('ConstraintValidator')
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
