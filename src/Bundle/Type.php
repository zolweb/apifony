<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

interface Type
{
    public function isNullable(): bool;

    public function getDefaultExpr(): Expr;

    public function getRouteRequirementPattern(): string;

    /**
     * @return list<Constraint>
     */
    public function getConstraints(): array;

    public function getBuiltInPhpType(): string;

    public function getInitValue(): Expr;

    public function getUsedModel(): ?string;

    /**
     * The components schemas referenced anywhere in this type, which the generated files must
     * import. Recursion stops at every reference: the referenced model imports its own dependencies.
     *
     * @return list<string>
     *
     * @throws Exception
     */
    public function getUsedModelNames(): array;

    public function getDocAst(): TypeNode;

    /**
     * True when getDocAst() carries information asName() does not, i.e. when the generated handler
     * method deserves a "param" tag.
     */
    public function hasInformativeDocType(): bool;

    /**
     * Statements converting the $source expression, as read from a query string, into a value of
     * this type assigned to $target.
     *
     * @return list<Stmt>
     *
     * @throws Exception
     */
    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array;

    public function asName(): Name;
}
