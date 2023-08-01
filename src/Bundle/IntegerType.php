<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Schema;
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

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'int';
    }

    public function getMethodParameterType(): string
    {
        return 'int';
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->schema->default !== null ? (string)$this->schema->default : null;
    }

    public function getRouteRequirementPattern(): string
    {
        return '-?(0|[1-9]\d*)';
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'intval';
    }

    public function getNormalizedType(): string
    {
        return 'Integer';
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return 'is_int($requestBodyPayload)';
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        if ($this->schema->multipleOf !== null) {
            $constraints[] = new Constraint('Assert\DivisibleBy', ['value' => $this->schema->multipleOf]);
        }

        if ($this->schema->minimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThanOrEqual', ['value' => $this->schema->minimum]);
        }

        if ($this->schema->maximum !== null) {
            $constraints[] = new Constraint('Assert\LessThanOrEqual', ['value' => $this->schema->maximum]);
        }

        if ($this->schema->exclusiveMinimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThan', ['value' => $this->schema->exclusiveMinimum]);
        }

        if ($this->schema->exclusiveMaximum !== null) {
            $constraints[] = new Constraint('Assert\LessThan', ['value' => $this->schema->exclusiveMaximum]);
        }

        if (count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'int';
    }

    public function getInitValue(): string
    {
        return '0';
    }

    public function getUsedModel(): ?string
    {
        return null;
    }
}