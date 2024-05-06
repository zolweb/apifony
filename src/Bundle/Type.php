<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

interface Type
{
    public function isNullable(): bool;

    public function getMethodParameterDefault(): Expr;

    public function getRouteRequirementPattern(): string;

    public function getStringToTypeCastFunction(): string;

    public function getNormalizedType(): string;

    public function getRequestBodyPayloadTypeChecking(): string;

    // todo take ast variable in parameter
    public function getRequestBodyPayloadTypeCheckingAst(): Expr;

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array;

    public function getBuiltInPhpType(): string;

    public function getInitValue(): Expr;

    public function getUsedModel(): ?string;

    public function getDocAst(): TypeNode;

    public function asName(): Name;
}
