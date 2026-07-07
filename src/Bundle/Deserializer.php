<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;

class Deserializer implements File
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
        return 'Deserializer.php';
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $f->namespace("{$this->bundleNamespace}\\Api")
                ->addStmt($f->use('Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor'))
                ->addStmt($f->use('Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor'))
                ->addStmt($f->use('Symfony\Component\PropertyInfo\PropertyInfoExtractor'))
                ->addStmt($f->use('Symfony\Component\Serializer\Encoder\JsonEncoder'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\ArrayDenormalizer'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\ObjectNormalizer'))
                ->addStmt($f->use('Symfony\Component\Serializer\Serializer'))
                ->addStmt($f->use('Symfony\Component\Serializer\SerializerInterface'))
                ->addStmt(
                    $f->class('Deserializer')->implement('DeserializerInterface')
                        ->addStmt($f->property('serializer')->makePrivate()->setType('SerializerInterface&DenormalizerInterface'))
                        ->addStmt(
                            $f->method('__construct')->makePublic()
                                ->addStmt(new Expression(new Assign($f->propertyFetch($f->var('this'), 'serializer'), new New_(new Name('Serializer'), $f->args([
                                    'normalizers' => new Array_([
                                        new ArrayItem(new New_(new Name('ObjectNormalizer'), $f->args([
                                            'propertyTypeExtractor' => new New_(new Name('PropertyInfoExtractor'), $f->args(['typeExtractors' => new Array_([new ArrayItem(new New_(new Name('PhpStanExtractor'))), new ArrayItem(new New_(new Name('ReflectionExtractor')))], ['kind' => Array_::KIND_SHORT])])),
                                        ]))),
                                        new ArrayItem(new New_(new Name('ArrayDenormalizer'))),
                                    ], ['kind' => Array_::KIND_SHORT]),
                                    'encoders' => new Array_([
                                        new ArrayItem(new New_(new Name('JsonEncoder'))),
                                    ], ['kind' => Array_::KIND_SHORT]),
                                ]))))),
                        )
                        ->addStmt(
                            $f->method('deserialize')
                                ->makePublic()
                                ->addParam($f->param('json')->setType('string'))
                                ->addParam($f->param('type')->setType('string'))
                                ->setReturnType('object')
                                ->addStmt(new Expression(new Assign($f->var('result'), $f->methodCall($f->propertyFetch($f->var('this'), 'serializer'), 'deserialize', $f->args([$f->var('json'), $f->var('type'), $f->classConstFetch('JsonEncoder', 'FORMAT')])))))
                                ->addStmts($this->getResultTypeGuardStmts($f))
                                ->addStmt(new Return_($f->var('result')))
                        )
                        ->addStmt(
                            $f->method('denormalize')
                                ->makePublic()
                                ->addParam($f->param('data')->setType('array'))
                                ->addParam($f->param('type')->setType('string'))
                                ->setReturnType('object')
                                ->addStmt(new Expression(new Assign($f->var('result'), $f->methodCall($f->propertyFetch($f->var('this'), 'serializer'), 'denormalize', $f->args([$f->var('data'), $f->var('type'), $f->val(null), new Array_([new ArrayItem($f->val(true), $f->classConstFetch('AbstractObjectNormalizer', 'DISABLE_TYPE_ENFORCEMENT'))], ['kind' => Array_::KIND_SHORT])])))))
                                ->addStmts($this->getResultTypeGuardStmts($f))
                                ->addStmt(new Return_($f->var('result')))
                        )
                )
                ->getNode(),
        ]);
    }

    /**
     * @return array<Stmt>
     */
    private function getResultTypeGuardStmts(BuilderFactory $f): array
    {
        return [
            new If_(new BooleanNot(new Instanceof_($f->var('result'), $f->var('type'))), ['stmts' => [
                new Expression(new Throw_($f->new('\RuntimeException', [new Encapsed([
                    new EncapsedStringPart('The deserialized value is not an instance of \''),
                    $f->var('type'),
                    new EncapsedStringPart('\'.'),
                ])]))),
            ]]),
        ];
    }
}
