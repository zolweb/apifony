<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\PrettyPrinter\Standard;

class DeserializerInterface implements File
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
        return 'DeserializerInterface.php';
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $f->namespace("{$this->bundleNamespace}\\Api")
                ->addStmt($f->use('Symfony\Component\Serializer\Exception\ExceptionInterface'))
                ->addStmt($f->interface('DeserializerInterface')
                    ->addStmt($f->method('deserialize')
                        ->setDocComment(<<<'COMMENT'
                            /**
                             * @template T of object
                             *
                             * @param class-string<T> $type
                             *
                             * @return T
                             */
                            COMMENT
                        )
                        ->makePublic()
                        ->addParam($f->param('json')->setType('string'))
                        ->addParam($f->param('type')->setType('string'))
                        ->setReturnType('object'),
                    )
                    ->addStmt($f->method('denormalize')
                        ->setDocComment(<<<'COMMENT'
                            /**
                             * @template T of object
                             *
                             * @param array<mixed> $data
                             * @param class-string<T> $type
                             *
                             * @return T
                             *
                             * @throws ExceptionInterface
                             */
                            COMMENT
                        )
                        ->makePublic()
                        ->addParam($f->param('data')->setType('array'))
                        ->addParam($f->param('type')->setType('string'))
                        ->setReturnType('object'),
                    ),
                )
                ->getNode(),
        ]);
    }
}
