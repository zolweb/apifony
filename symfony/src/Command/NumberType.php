<?php

namespace App\Command;

class NumberType implements Type
{
    public function getPhpDocParameterAnnotationType(Schema $schema): string
    {
        return 'float';
    }

    public function getMethodParameterType(Schema $schema): string
    {
        return 'float';
    }

    public function getMethodParameterDefault(Schema $schema): ?string
    {
        return $schema->default !== null ? (string)$schema->default : null;
    }

    public function getRouteRequirementPattern(Schema $schema): string
    {
        return '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?';
    }

    public function getStringToTypeCastFunction(Schema $schema): string
    {
        return 'floatval';
    }

    public function getContentInitializationFromRequest(Schema $schema): string
    {
        return '$content = json_decode($request->getContent(), true);';
    }

    public function getContentValidationViolationsInitialization(Schema $schema): string
    {
        return sprintf(
            "\$violations = \$validator->validate(\$content, [\n%s\n]);",
            implode(
                '',
                array_map(
                    static fn (Constraint $constraint) => $constraint->getInstantiation(5),
                    $this->getConstraints($schema),
                ),
            ),
        );
    }

    public function getNormalizedType(Schema $schema): string
    {
        return 'Float';
    }

    public function getContentTypeChecking(Schema $schema): string
    {
        return 'is_float($content)';
    }

    public function getConstraints(Schema $schema): array
    {
        $constraints = [];

        if ($schema->multipleOf !== null) {
            $constraints[] = new Constraint('Assert\DivisibleBy', ['value' => $schema->multipleOf]);
        }

        if ($schema->minimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThanOrEqual', ['value' => $schema->minimum]);
        }

        if ($schema->maximum !== null) {
            $constraints[] = new Constraint('Assert\LessThanOrEqual', ['value' => $schema->maximum]);
        }

        if ($schema->exclusiveMinimum !== null) {
            $constraints[] = new Constraint('Assert\GreaterThan', ['value' => $schema->exclusiveMinimum]);
        }

        if ($schema->exclusiveMaximum !== null) {
            $constraints[] = new Constraint('Assert\LessThan', ['value' => $schema->exclusiveMaximum]);
        }

        if ($schema->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $schema->enum]);
        }

        return $constraints;
    }

    public function getFiles(Schema $schema): array
    {
        return [];
    }

    public function __toString(): string
    {
        return 'number';
    }
}