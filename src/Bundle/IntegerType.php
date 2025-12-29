<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\Narrow;
use Zol\Apifony\OpenApi\Schema;

use function Symfony\Component\String\u;

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

    public function getRequestBodyPayloadTypeCheckingAst(): Expr
    {
        $f = new BuilderFactory();

        return $f->funcCall('is_int', [$f->var('requestBodyPayload')]);
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        if ($this->schema->multipleOf !== null) {
            $constraints[] = new Constraint('Assert\DivisibleBy', ['value' => $this->schema->multipleOf]);
        }

        $constraints[] = new Constraint('Assert\GreaterThanOrEqual', ['value' => $this->getMin()]);
        $constraints[] = new Constraint('Assert\LessThanOrEqual', ['value' => $this->getMax()]);

        if (\count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
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
            return new IdentifierTypeNode(implode('|', $this->schema->enum));
        }

        $type = new IdentifierTypeNode(\sprintf(
            'int<%d,%d>',
            $this->getMin(),
            $this->getMax(),
        ));

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
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
