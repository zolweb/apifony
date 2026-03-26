<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\Expression;
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

        $addViolation = static fn (string $message) => new Expression($f->methodCall(
            $f->methodCall(
                $f->propertyFetch($f->var('this'), 'context'),
                'buildViolation',
                [$f->val($message)],
            ),
            'addViolation',
        ));

        $validateMethod = $f->method('validate')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraint')->setType('Constraint'))
            ->setReturnType('void')
            ->addStmt(new If_($f->funcCall('\is_string', [$f->var('value')]), ['stmts' => [
                new If_($f->funcCall('\strlen', [$f->var('value')]), [
                    'stmts' => [
                        new Expression(new Assign(
                            $f->var('emailValidator'),
                            new New_(new Name('EguliasEmailValidator')),
                        )),
                        new If_(new BooleanNot($f->methodCall($f->var('emailValidator'), 'isValid', [
                            $f->var('value'),
                            new New_(new Name('RFCValidation')),
                        ])), ['stmts' => [
                            $addViolation('This value is not a valid email address.'),
                        ]]),
                    ],
                    'else' => new Else_([
                        $addViolation('This value should not be blank.'),
                    ]),
                ]),
            ]]))
        ;

        $class = $f->class('EmailValidator')
            ->extend('ConstraintValidator')
            ->addStmt($validateMethod)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Format")
            ->addStmt($f->use('Egulias\EmailValidator\EmailValidator as EguliasEmailValidator'))
            ->addStmt($f->use('Egulias\EmailValidator\Validation\RFCValidation'))
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
