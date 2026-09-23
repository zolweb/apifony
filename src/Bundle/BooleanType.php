<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\OpenApi\Schema;

class BooleanType implements Type
{
    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
    ) {
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getDefaultExpr(): Expr
    {
        if (!$this->schema->hasDefault) {
            throw new \RuntimeException();
        }

        switch (true) {
            case $this->schema->default === true:
                return new ConstFetch(new Name('true'));
            case $this->schema->default === false:
                return new ConstFetch(new Name('false'));
            case $this->schema->default === null && $this->nullable:
                return new ConstFetch(new Name('null'));
            default:
                throw new \RuntimeException();
        }
    }

    public function getRouteRequirementPattern(): string
    {
        return 'true|false';
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', [], enforcedByDenormalizer: true);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', Naming::forClass($this->schema->format)), [], $this->schema->format);
        }

        if (\count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum], enforcedByDenormalizer: true);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'bool';
    }

    public function getInitValue(): Expr
    {
        return new ConstFetch(new Name('false'));
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    public function getDocAst(): TypeNode
    {
        if (\count($this->schema->enum) > 0) {
            $values = [];
            foreach ($this->schema->enum as $e) {
                switch (true) {
                    case \is_bool($e):
                        $values[] = $e ? 'true' : 'false';
                        break;
                    case $e === null && $this->nullable:
                        $values[] = 'null';
                        break;
                    default:
                        throw new \RuntimeException();
                }
            }

            return new IdentifierTypeNode(implode('|', $values));
        }

        $type = new IdentifierTypeNode('bool');

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function getUsedModelNames(): array
    {
        return [];
    }

    public function hasInformativeDocType(): bool
    {
        return false;
    }

    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        return $context->wrapNullable($this->nullable, $source, $target, fn (Expr $value): array => array_merge(
            [new Expression(new Assign($target, $f->methodCall($f->var('this'), \sprintf('denormalize%s%s', ucfirst($this->getBuiltInPhpType()), $context->getSource()), [$value, $path])))],
            $this->getNarrowingStmts($target, $path, $context),
        ));
    }

    /**
     * Statements making the value actually satisfy the narrowed type getDocAst() advertises, so
     * that the generated models can be constructed without asserting anything.
     *
     * @return list<Stmt>
     */
    private function getNarrowingStmts(Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        if (\count($this->schema->enum) === 0) {
            return [];
        }

        $expectation = \sprintf('This value should be one of %s.', implode(', ', array_map(
            static fn (mixed $e): string => $e === null ? 'null' : var_export($e, true),
            $this->schema->enum,
        )));

        return [new If_(
            new BooleanNot($f->funcCall('\in_array', [
                $target,
                new Array_(array_map(static fn (mixed $e): ArrayItem => new ArrayItem($f->val($e)), $this->schema->enum), ['kind' => Array_::KIND_SHORT]),
                $f->val(true),
            ])),
            ['stmts' => [new Expression(new Throw_($f->new('DenormalizationException', [
                $path, $f->val('invalid_enum_value'), $f->val($expectation),
            ])))]],
        )];
    }

    public function asName(): Name
    {
        return new Name($this->nullable ? '?bool' : 'bool');
    }
}
