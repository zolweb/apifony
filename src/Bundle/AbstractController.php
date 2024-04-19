<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;

class AbstractController implements File
{
    public static function build(string $bundleNamespace): self
    {
        return new self($bundleNamespace);
    }

    private function __construct(
        private readonly string $bundleNamespace,
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api";
    }

    public function getFolder(): string
    {
        return 'src/Api';
    }

    public function getName(): string
    {
        return 'AbstractController.php';
    }

    public function getTemplate(): string
    {
        return 'abstract-controller.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'abstractController';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
            ->addParam($f->param('serializer')->setType('SerializerInterface')->makeProtected()->makeReadonly())
            ->addParam($f->param('validator')->setType('ValidatorInterface')->makeProtected()->makeReadonly());

        $validateParameter = $f->method('validateParameter')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraints')->setType('array'))
            ->setReturnType('void')
            ->setDocComment(<<<'COMMENT'
                /**
                 * @param array<Constraint> $constraints
                 *
                 * @throws ParameterValidationException
                 */
                COMMENT
            )
            ->addStmt(new Assign($f->var('violations'), $f->methodCall($f->propertyFetch($f->var('this'), 'validator'), 'validate', [$f->var('value'), $f->var('constraints')])))
            ->addStmt(new If_(new Greater($f->funcCall('count', [$f->var('violations')]), $f->val(0)), ['stmts' => [
                new Expression(new Throw_($f->new('ParameterValidationException', [
                    $f->funcCall('array_map', [
                        new ArrowFunction(['static' => true, 'params' => [$f->param('violation')->setType('ConstraintViolationInterface')->getNode()], 'expr' => $f->methodCall($f->var('violation'), 'getMessage')]),
                        $f->funcCall('iterator_to_array', [$f->var('violations')]),
                    ]),
                ]))),
            ]]));

        $validateRequestBody = $f->method('validateRequestBody')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('constraints')->setType('array'))
            ->setReturnType('void')
            ->setDocComment(<<<'COMMENT'
                /**
                 * @param array<Constraint> $constraints
                 *
                 * @throws RequestBodyValidationException
                 */
                COMMENT
            )
            ->addStmt(new Assign($f->var('violations'), $f->methodCall($f->propertyFetch($f->var('this'), 'validator'), 'validate', [$f->var('value'), $f->var('constraints')])))
            ->addStmt(new If_(new Greater($f->funcCall('count', [$f->var('violations')]), $f->val(0)), ['stmts' => [
                new Expression(new Assign($f->var('errors'), $f->val([]))),
                new Foreach_($f->var('violations'), $f->var('violation'), ['stmts' => [
                    new Expression(new Assign($f->var('path'), $f->methodCall($f->var('violation'), 'getPropertyPath'))),
                    new If_(new BooleanNot($f->funcCall('isset', [new ArrayDimFetch($f->var('errors'), $f->var('path'))])), ['stmts' => [
                        new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->var('path')), $f->val([]))),
                    ]]),
                    new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->var('path'))), new String_($f->methodCall($f->var('violation'), 'getMessage')))),
                ]]),
                new Expression(new Throw_($f->new('RequestBodyValidationException', [$f->var('errors')]))),
            ]]));

        $class = $f->class('AbstractController')
            ->makeAbstract()
            ->addStmt($constructor);

        foreach (['string', 'int', 'float', 'bool'] as $type) {
            foreach ([false, true] as $nullable) {
                $getParameterMethod = $f->method(sprintf('get%s%sParameter', ucfirst($type), $nullable ? 'OrNull' : ''))
                    ->makePublic()
                    ->addParam($f->param('request')->setType('Request'))
                    ->addParam($f->param('name')->setType('string'))
                    ->addParam($f->param('in')->setType('string'))
                    ->addParam($f->param('required')->setType('bool'))
                    ->addParam($f->param('default')->setType("?{$type}")->setDefault(null))
                    ->setReturnType(sprintf("%s{$type}", $nullable ? '?' : ''))
                    ->setDocComment(<<<'COMMENT'
                        /**
                         * @throws DenormalizationException
                         */
                        COMMENT
                    )
                    ->addStmt(new Expression(new Assign($f->var('bag'), new Match_($f->var('in'), [
                        new MatchArm([$f->val('query')], $f->propertyFetch($f->var('request'), 'query')),
                        new MatchArm([$f->val('header')], $f->propertyFetch($f->var('request'), 'headers')),
                        new MatchArm([$f->val('cookie')], $f->propertyFetch($f->var('request'), 'cookies')),
                        new MatchArm(null, new Throw_($f->new('\RuntimeException', [$f->val('Invalid parameter location.')]))),
                    ]))))
                    ->addStmt(new Expression(new Assign($f->var('isset'), $f->methodCall($f->var('bag'), 'has', [$f->var('name')]))))
                    ->addStmt(new Expression(new Assign($f->var('value'), $f->methodCall($f->var('bag'), 'get', [$f->var('name')]))))
                    ->addStmt(new If_(new BooleanNot($f->var('isset')), ['stmts' => array_filter([
                        new If_($f->var('required'), ['stmts' => [
                            new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' is required.')])]))),
                        ]]),
                        $nullable ?
                            null :
                            new If_(new Identical($f->var('default'), $f->val(null)), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' must not be null.')])]))),
                            ]]),
                        new Return_($f->var('default')),
                    ])]))
                    ->addStmt(new If_(new Identical($f->val('value'), $f->val(null)), ['stmts' => [
                        $nullable ?
                            new Return_($f->val(null)) :
                            new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' must not be null.')])]))),
                    ]]))
                    ->addStmts(match($type) {
                        'string' => [
                            new Return_($f->var('value')),
                        ],
                        'int' => [
                            new If_(new BooleanNot($f->funcCall('ctype_digit', [$f->var('value')])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' must be an integer.')])]))),
                            ]]),
                            new Return_($f->funcCall('intval', [$f->var('value')])),
                        ],
                        'float' => [
                            new If_(new BooleanNot($f->funcCall('is_numeric', [$f->var('value')])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' must be a numeric.')])]))),
                            ]]),
                            new Return_($f->funcCall('floatval', [$f->var('value')])),
                        ],
                        'bool' => [
                            new If_(new BooleanNot($f->funcCall('in_array', [$f->var('value'), $f->val(['true', 'false']), $f->val(true)])), ['stmts' => [
                                new Expression(new Throw_($f->new('DenormalizationException', [new InterpolatedString([new InterpolatedStringPart('Parameter '), $f->var('name'), new InterpolatedStringPart(' in '), $f->var('in'), new InterpolatedStringPart(' must be a boolean.')])]))),
                            ]]),
                            new Return_(new ArrayDimFetch(new Array_([new ArrayItem($f->val(true), $f->val('true')), new ArrayItem($f->val(false), $f->val('false'))]), $f->var('value'))),
                        ],
                    });

                $class->addStmt($getParameterMethod);
            }
        }

        $class->addStmt($validateParameter)
            ->addStmt($validateRequestBody);

        $namespace = $f->namespace("{$this->bundleNamespace}\Api")
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\Serializer\Encoder\JsonEncoder'))
            ->addStmt($f->use('Symfony\Component\Serializer\SerializerInterface'))
            ->addStmt($f->use('Symfony\Component\Validator\Constraint'))
            ->addStmt($f->use('Symfony\Component\Validator\ConstraintViolationInterface'))
            ->addStmt($f->use('Symfony\Component\Validator\Validator\ValidatorInterface'))
            ->addStmt($f->use('Symfony\Component\Serializer\Exception\ExceptionInterface'))
            ->addStmt($class);

        return $namespace->getNode();
    }
}