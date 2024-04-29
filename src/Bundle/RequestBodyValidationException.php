<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;

class RequestBodyValidationException implements File
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
        return 'RequestBodyValidationException.php';
    }

    public function getTemplate(): string
    {
        return 'request-body-validation-exception.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'exception';
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
            ->addParam($f->param('messages')->setType('array')->makePublic()->makeReadonly())
            ->addStmt($f->staticCall('parent', '__construct'))
            ->setDocComment(<<<'COMMENT'
                /**
                 * @param array<string, string[]> $messages
                 */
                COMMENT
            );

        $class = $f->class('RequestBodyValidationException')
            ->extend('\Exception')
            ->addStmt($constructor);

        $namespace = $f->namespace("{$this->bundleNamespace}\Api")
            ->addStmt($class);

        return (new Standard)->prettyPrintFile([$namespace->getNode()]);
    }
}