<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Namespace_;

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

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct');
        $constructor->makePublic();
        $constructor->addParam($f->param('message')->setType('string'));
        $constructor->addStmt($f->staticCall('parent', '__construct', [$f->var('message')]));

        $class = $f->class('DenormalizationException');
        $class->extend('\Exception');
        $class->addStmt($constructor);

        $namespace = $f->namespace("{$this->bundleNamespace}\Api");
        $namespace->addStmt($class);

        return $namespace->getNode();
    }
}