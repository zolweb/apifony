<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Namespace_;

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

    public function getTemplate(): string
    {
        return 'parameter-validation-exception.php.twig';
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
        $constructor->addParam($f->param('messages')->setType('array')->makePublic()->makeReadonly());
        $constructor->addStmt($f->staticCall('parent', '__construct'));
        $constructor->setDocComment(<<<'COMMENT'
            /**
             * @param string[] $messages
             */
            COMMENT
        );

        $class = $f->class('ParameterValidationException');
        $class->extend('\Exception');
        $class->addStmt($constructor);

        $namespace = $f->namespace("{$this->bundleNamespace}\Api");
        $namespace->addStmt($class);

        return $namespace->getNode();
    }
}