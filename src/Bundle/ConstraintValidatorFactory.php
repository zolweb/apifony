<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_ as ExprThrow_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;

class ConstraintValidatorFactory implements File
{
    public function __construct(
        private readonly string $bundleNamespace,
    ) {
    }

    public function getFolder(): string
    {
        return 'src/Api';
    }

    public function getName(): string
    {
        return 'ConstraintValidatorFactory.php';
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $f->namespace("{$this->bundleNamespace}\\Api")
                ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
                ->addStmt($f->use('Symfony\Component\Validator\ConstraintValidatorInterface'))
                ->addStmt($f->use('Symfony\Component\Validator\ConstraintValidatorFactoryInterface'))
                ->addStmt($f->class('ConstraintValidatorFactory')
                    ->makeFinal()
                    ->implement('ConstraintValidatorFactoryInterface')
                    ->addStmt($f->property('validators')
                        ->makePrivate()
                        ->setType('array')
                        ->setDefault(new Array_([], ['kind' => Array_::KIND_SHORT]))
                        ->setDocComment(<<<'COMMENT'
                            /** @var array<string, ConstraintValidatorInterface> */
                            COMMENT
                        )
                    )
                    ->addStmt($f->method('addValidator')
                        ->makePublic()
                        ->setReturnType('void')
                        ->addParam($f->param('validator')->setType('ConstraintValidatorInterface'))
                        ->addStmt(new Expression(new Assign(
                            new ArrayDimFetch(
                                $f->propertyFetch($f->var('this'), 'validators'),
                                $f->classConstFetch(new Variable('validator'), 'class'),
                            ),
                            $f->var('validator'),
                        )))
                    )
                    ->addStmt($f->method('getInstance')
                        ->makePublic()
                        ->setReturnType('ConstraintValidatorInterface')
                        ->addParam($f->param('constraint')->setType('Constraint'))
                        ->addStmt(new Expression(new Assign(
                            $f->var('name'),
                            $f->methodCall($f->var('constraint'), 'validatedBy'),
                        )))
                        ->addStmt(new If_(
                            new BooleanNot($f->funcCall('isset', [
                                new ArrayDimFetch(
                                    $f->propertyFetch($f->var('this'), 'validators'),
                                    $f->var('name'),
                                ),
                            ])),
                            ['stmts' => [
                                new Expression(new Assign(
                                    $f->var('validator'),
                                    new New_(new Name('$name')),
                                )),
                                new If_(
                                    new BooleanNot(new Instanceof_(
                                        $f->var('validator'),
                                        new Name('ConstraintValidatorInterface'),
                                    )),
                                    ['stmts' => [
                                        new Expression(new ExprThrow_(new New_(new Name('\RuntimeException')))),
                                    ]],
                                ),
                                new Expression(new Assign(
                                    new ArrayDimFetch(
                                        $f->propertyFetch($f->var('this'), 'validators'),
                                        $f->var('name'),
                                    ),
                                    $f->var('validator'),
                                )),
                            ]],
                        ))
                        ->addStmt(new Return_(new ArrayDimFetch(
                            $f->propertyFetch($f->var('this'), 'validators'),
                            $f->var('name'),
                        )))
                    )
                )
                ->getNode(),
        ]);
    }
}
