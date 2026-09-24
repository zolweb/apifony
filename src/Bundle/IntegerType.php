<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\Narrow;
use Zol\Apifony\Resolved\Schema;

class IntegerType implements Type
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

        if ($this->schema->default === null) {
            return new ConstFetch(new Name('null'));
        }

        if (!\is_int($this->schema->default)) {
            throw new \RuntimeException();
        }

        return new LNumber($this->schema->default);
    }

    public function getRouteRequirementPattern(): string
    {
        return '-?(0|[1-9]\d*)';
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

        if ($this->schema->multipleOf !== null) {
            $constraints[] = new Constraint('Assert\DivisibleBy', ['value' => $this->schema->multipleOf]);
        }

        if (($min = $this->getMin()) !== \PHP_INT_MIN) {
            $constraints[] = new Constraint('Assert\GreaterThanOrEqual', ['value' => $min], enforcedByDenormalizer: true);
        }

        if (($max = $this->getMax()) !== \PHP_INT_MAX) {
            $constraints[] = new Constraint('Assert\LessThanOrEqual', ['value' => $max], enforcedByDenormalizer: true);
        }

        if (\count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum], enforcedByDenormalizer: true);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'int';
    }

    public function getInitValue(): Expr
    {
        return new LNumber(0);
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
                    case \is_int($e):
                        $values[] = (string) $e;
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

        $type = new IdentifierTypeNode(\sprintf(
            'int<%s,%s>',
            ($min = $this->getMin()) === \PHP_INT_MIN ? 'min' : (string) $min,
            ($max = $this->getMax()) === \PHP_INT_MAX ? 'max' : (string) $max,
        ));

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

    public function getDenormalizationRootModels(): array
    {
        return [];
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
            $min = $this->getMin();
            $max = $this->getMax();
            if ($min === \PHP_INT_MIN && $max === \PHP_INT_MAX) {
                return [];
            }

            $condition = null;
            if ($min !== \PHP_INT_MIN) {
                $condition = new Smaller($target, $f->val($min));
            }
            if ($max !== \PHP_INT_MAX) {
                $greater = new Greater($target, $f->val($max));
                $condition = $condition === null ? $greater : new BooleanOr($condition, $greater);
            }
            if ($condition === null) {
                return [];
            }

            return [new If_($condition, ['stmts' => [new Expression(new Throw_($f->new('DenormalizationException', [
                $path, $f->val('out_of_range'), $f->val(\sprintf(
                    'This value should be %s.',
                    match (true) {
                        $min === \PHP_INT_MIN => \sprintf('at most %d', $max),
                        $max === \PHP_INT_MAX => \sprintf('at least %d', $min),
                        default => \sprintf('between %d and %d', $min, $max),
                    },
                )),
            ])))]])];
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
        return new Name($this->nullable ? '?int' : 'int');
    }

    private function getMin(): int
    {
        return Narrow::int(max(
            \PHP_INT_MIN,
            $this->schema->minimum ?? \PHP_INT_MIN,
            $this->schema->exclusiveMinimum !== null ? $this->schema->exclusiveMinimum + 1 : \PHP_INT_MIN,
        ));
    }

    private function getMax(): int
    {
        return Narrow::int(min(
            \PHP_INT_MAX,
            $this->schema->maximum ?? \PHP_INT_MAX,
            $this->schema->exclusiveMaximum !== null ? $this->schema->exclusiveMaximum - 1 : \PHP_INT_MAX,
        ));
    }
}
