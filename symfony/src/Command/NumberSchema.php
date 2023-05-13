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

    public function getConstraints(): array
    {
        return [];
        $constraints = [];

        if ($this->minimum !== null) {
            $constraints[] = sprintf(
                'Assert\GreaterThanOrEqual(%d)',
                $this->minimum,
            );
        }

        if ($this->maximum !== null) {
            $constraints[] = sprintf(
                'Assert\LessThanOrEqual(%d)',
                $this->maximum,
            );
        }

        if ($this->exclusiveMinimum !== null) {
            $constraints[] = sprintf(
                'Assert\GreaterThan(%d)',
                $this->exclusiveMinimum,
            );
        }

        if ($this->exclusiveMaximum !== null) {
            $constraints[] = sprintf(
                'Assert\LessThan(%d)',
                $this->exclusiveMaximum,
            );
        }

        if ($this->enum !== null) {
            $constraints[] = sprintf(
                'Assert\Choice([\'%s\'])',
                implode('\', \'', $this->enum),
            );
        }
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

    public function getFiles(): array
    {
        return [];
    }
}