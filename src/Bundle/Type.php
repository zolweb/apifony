<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

interface Type
{
    public function isNullable(): bool;

    public function getDefaultExpr(): Expr;

    public function getRouteRequirementPattern(): string;

    public function getNormalizedType(): string;

    // todo take ast variable in parameter
    public function getRequestBodyPayloadTypeCheckingAst(): Expr;

    /**
     * @return list<Constraint>
     */
    public function getConstraints(): array;

    public function getBuiltInPhpType(): string;

    public function getInitValue(): Expr;

    public function getUsedModel(): ?string;

    public function getDocAst(): TypeNode;

    public function asName(): Name;
}
