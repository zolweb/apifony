<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\Resolved\Schema;

/**
 * The type of an x-apifony-raw schema: the declared type is ignored and any value is accepted,
 * null included. It is rendered as mixed, no model is emitted for it, nothing is asserted about
 * it, and denormalizing it is a pass through.
 *
 * From a JSON document it carries whatever json_decode returned. From a query string it carries
 * whatever the bracket notation built: an arbitrarily nested structure, but one whose leaves are
 * always strings, since a query string cannot express any other scalar.
 */
class RawType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    /**
     * Raw accepts null like it accepts everything else. The generated PHP does not say so, since
     * mixed already includes null and cannot be marked nullable.
     */
    public function isNullable(): bool
    {
        return true;
    }

    public function getDefaultExpr(): Expr
    {
        if (!$this->schema->hasDefault) {
            throw new \RuntimeException();
        }

        return (new BuilderFactory())->val($this->schema->default);
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Raw path parameters are not supported.', $this->schema->path);
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getBuiltInPhpType(): string
    {
        return 'mixed';
    }

    public function getInitValue(): Expr
    {
        return new ConstFetch(new Name('null'));
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    public function getUsedModelNames(): array
    {
        return [];
    }

    public function getDocAst(): TypeNode
    {
        return new IdentifierTypeNode('mixed');
    }

    public function hasInformativeDocType(): bool
    {
        return false;
    }

    /**
     * Checking the shape of a raw value would defeat the point of declaring it raw.
     */
    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        return [new Expression(new Assign($target, $source))];
    }

    public function asName(): Name
    {
        return new Name('mixed');
    }
}
