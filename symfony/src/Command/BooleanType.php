<?php

namespace App\Command;

class BooleanType implements Type
{
    public function getPhpDocParameterAnnotationType(Schema $schema): string
    {
        return 'bool';
    }

    public function getMethodParameterType(Schema $schema): string
    {
        return 'bool';
    }

    public function getMethodParameterDefault(Schema $schema): ?string
    {
        return [true => 'true', false => 'false', null => null][$schema->default];
    }

    public function getRouteRequirementPattern(Schema $schema): string
    {
        return 'true|false';
    }

    public function getStringToTypeCastFunction(Schema $schema): string
    {
        return 'boolval';
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
        return 'Boolean';
    }

    public function getContentTypeChecking(Schema $schema): string
    {
        return 'is_bool($content)';
    }

    public function getConstraints(Schema $schema): array
    {
        $constraints = [];

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
        return 'boolean';
    }
}