<?php

namespace App\Command;

class NumberSchema extends Schema
{
    private readonly ?float $default;
    private readonly ?float $multipleOf;
    private readonly ?float $minimum;
    private readonly ?float $maximum;
    private readonly ?float $exclusiveMinimum;
    private readonly ?float $exclusiveMaximum;
    private readonly ?array $enum;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);
        $this->default = $data['default'] ?? null;
        $this->multipleOf = $data['multipleOf'] ?? null;
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

    public function getStringToTypeCastFunction(): string
    {
        return 'floatval';
    }

    public function getContentInitializationFromRequest(): string
    {
        return '$content = json_decode($request->getContent(), true);';
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return sprintf(
            "\$violations = \$validator->validate(\$content, [\n%s\n]);",
            implode(
                '',
                array_map(
                    static fn (Constraint $constraint) => $constraint->getInstantiation(5),
                    $this->getConstraints(),
                ),
            ),
        );
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->multipleOf !== null) {
            $constraints[] = new Constraint('Assert\DivisibleBy', ['value' => $this->multipleOf]);
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