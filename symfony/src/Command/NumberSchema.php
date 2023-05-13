<?php

namespace App\Command;

class NumberSchema extends Schema
{
    private readonly ?float $default;
    private readonly ?int $minimum;
    private readonly ?int $maximum;
    private readonly ?int $exclusiveMinimum;
    private readonly ?int $exclusiveMaximum;
    private readonly ?array $enum;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);
        $this->default = $data['default'] ?? null;
        $this->minimum = $data['minimum'] ?? null;
        $this->maximum = $data['maximum'] ?? null;
        $this->exclusiveMinimum = $data['exclusiveMinimum'] ?? null;
        $this->exclusiveMaximum = $data['exclusiveMaximum'] ?? null;
        $this->enum = $data['enum'] ?? null;
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'float';
    }

    public function getMethodParameterType(): string
    {
        return 'float';
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->default !== null ? (string)$this->default : null;
    }

    public function getRouteRequirement(): string
    {
        return "'{$this->name}' => '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?',";
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->minimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThanOrEqual', ['value' => $this->minimum]);
        }

        if ($this->maximum !== null) {
            $constraints[] = new Constraint('Assert\LessThanOrEqual', ['value' => $this->maximum]);
        }

        if ($this->exclusiveMinimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThan', ['value' => $this->exclusiveMinimum]);
        }

        if ($this->exclusiveMaximum !== null) {
            $constraints[] = new Constraint('Assert\LessThan', ['value' => $this->exclusiveMaximum]);
        }

        if ($this->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->enum]);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return [];
    }
}