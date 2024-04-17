<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

class ComposerJson implements File
{
    public function __construct(
        private readonly string $packageName,
        private readonly string $namespace,
    ) {
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFolder(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'composer.json';
    }

    public function getTemplate(): string
    {
        return 'composer.json.twig';
    }

    public function getParametersRootName(): string
    {
        return 'composer';
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