<?php

namespace App\Command;

class NumberType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
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
        return $this->schema->default !== null ? (string)$this->schema->default : null;
    }

    public function getRouteRequirementPattern(): string
    {
        return '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?';
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

    public function getNormalizedType(): string
    {
        return 'Float';
    }

    public function getContentTypeChecking(): string
    {
        return 'is_float($content)';
    }

    public function getConstraints(): array
    {
        $constraints = [];

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

        if ($this->schema->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return 'number';
    }
}