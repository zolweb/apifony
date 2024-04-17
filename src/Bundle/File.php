<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

interface File
{
    public function getFolder(): string;

    public function getName(): string;

    public function getTemplate(): string;

    public function getParametersRootName(): string;

    public function hasNamespaceAst(): bool;

    public function getNamespaceAst(): Namespace_;
}