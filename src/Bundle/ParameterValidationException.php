<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

class ParameterValidationException implements File
{
    public function __construct(
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
        return false;
    }

    public function getNamespaceAst(): Namespace_
    {
        throw new \RuntimeException();
    }
}