<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Parameter;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

use function Symfony\Component\String\u;

class ActionParameter
{
    /**
     * @throws Exception
     */
    public static function build(
        string $actionClassName,
        Reference|Parameter $parameter,
        ?Components $components,
        int $ordinal,
    ): self {
        if ($parameter instanceof Reference) {
            if ($components === null || !isset($components->parameters[$parameter->getName()])) {
                throw new Exception('Reference not found in parameters components.');
            }
            $parameter = $components->parameters[$parameter->getName()];
        }
        if ($parameter->schema === null) {
            throw new Exception('Parameter objects without schema attribute are not supported.');
        }
        if ($parameter->schema instanceof Reference) {
            throw new \RuntimeException();
        }
        $variableName = sprintf('%s%s', $parameter->in[0], u($parameter->name)->camel()->title());
        $className = "{$actionClassName}_{$parameter->name}";
        $className = u($className)->camel()->title()->toString();

        return new self(
            $variableName,
            $parameter,
            TypeFactory::build($className, $parameter->schema, $components),
            $parameter->schema,
            $ordinal,
        );
    }

    private function __construct(
        private readonly string $variableName,
        private readonly Parameter $parameter,
        private readonly Type $type,
        private readonly Schema $schema,
        private readonly int $ordinal,
    ) {
    }

    public function getIn(): string
    {
        return $this->parameter->in;
    }

    public function hasDefault(): bool
    {
        return $this->schema->hasDefault;
    }

    public function getDefault(): Expr
    {
        return $this->type->getDefaultExpr();
    }

    public function getRawName(): string
    {
        return $this->parameter->name;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function isNullable(): bool
    {
        return $this->type->isNullable();
    }

    public function getPhpType(): string
    {
        return $this->type->asName()->toString();
    }

    public function isRequired(): bool
    {
        return $this->parameter->required;
    }

    public function getRouteRequirementPattern(): string
    {
        return $this->type->getRouteRequirementPattern();
    }

    public function getInitValueAst(): Expr
    {
        return $this->type->getInitValue();
    }

    public function asString(): String_
    {
        return new String_($this->parameter->name);
    }

    public function asVariable(bool $rawName = false): Variable
    {
        return new Variable($rawName ? $this->parameter->name : $this->variableName);
    }

    public function asParam(bool $rawName = false): Param
    {
        return new Param(
            var: new Variable($rawName ? $this->parameter->name : $this->variableName),
            type: $this->type->asName(),
        );
    }

    public function shouldBePositionedBefore(self $other): bool
    {
        if ($this->schema->hasDefault !== $other->schema->hasDefault) {
            return $other->schema->hasDefault;
        }

        return $this->ordinal < $other->ordinal;
    }
}
