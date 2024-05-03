<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\PrettyPrinter\Standard;

class ParameterValidationException implements File
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
        return 'ParameterValidationException.php';
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
                 * @param string[] $messages
                 */
                COMMENT
            )
        ;

        $class = $f->class('ParameterValidationException')
            ->extend('\Exception')
            ->addStmt($constructor)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api")
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareItem('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
