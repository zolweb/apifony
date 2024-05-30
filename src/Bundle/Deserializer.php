<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
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
                ->addStmt($f->use('Symfony\Component\Serializer\Encoder\JsonEncoder'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\ArrayDenormalizer'))
                ->addStmt($f->use('Symfony\Component\Serializer\Normalizer\ObjectNormalizer'))
                ->addStmt($f->use('Symfony\Component\Serializer\Serializer'))
                ->addStmt($f->use('Symfony\Component\Serializer\SerializerInterface'))
                ->addStmt($f->class('Deserializer')->implement('DeserializerInterface')
                    ->addStmt($f->property('serializer')->makePrivate()->setType('SerializerInterface'))
                    ->addStmt($f->method('__construct')->makePublic()
                        ->addStmt(new Expression(new Assign($f->propertyFetch($f->var('this'), 'serializer'), new New_(new Name('Serializer'), $f->args([
                            'normalizers' => new Array_([
                                new ArrayItem(new New_(new Name('ObjectNormalizer'), $f->args([
                                    'propertyTypeExtractor' => new New_(new Name('PhpStanExtractor')),
                                ]))),
                                new ArrayItem(new New_(new Name('ArrayDenormalizer'))),
                            ]),
                            'encoders' => new Array_([
                                new ArrayItem(new New_(new Name('JsonEncoder'))),
                            ]),
                        ]))))),
                    )
                    ->addStmt($f->method('deserialize')
                        ->makePublic()
                        ->addParam($f->param('json')->setType('string'))
                        ->addParam($f->param('type')->setType('string'))
                        ->setReturnType('object')
                        ->addStmt(new Return_($f->methodCall($f->propertyFetch($f->var('this'), 'serializer'), 'deserialize', $f->args([$f->var('json'), $f->var('type'), $f->classConstFetch('JsonEncoder', 'FORMAT')]))))
                    )
                )
                ->getNode(),
        ]);
    }
}
