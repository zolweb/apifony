<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\PrettyPrinter\Standard;

class DenormalizationException implements File
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
        return 'DenormalizationException.php';
    }

    public function getTemplate(): string
    {
        return 'denormalization-exception.php.twig';
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
            ->addParam($f->param('message')->setType('string'))
            ->addStmt($f->staticCall('parent', '__construct', [$f->var('message')]))
        ;

        $class = $f->class('DenormalizationException')
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
