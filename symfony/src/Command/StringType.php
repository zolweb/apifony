<?php

namespace App\Command;

class StringType implements Type
{
    public function getPhpDocParameterAnnotationType(Schema $schema): string
    {
        return 'string';
    }

    public function getMethodParameterType(Schema $schema): string
    {
        return 'string';
    }

    public function getMethodParameterDefault(Schema $schema): ?string
    {
        assert(is_string($schema->default));

        return sprintf('\'%s\'', str_replace('\'', '\\\'', $schema->default));
    }

    public function getRouteRequirementPattern(Schema $schema): string
    {
        return $schema->pattern !== null ? $schema->pattern : '[^:/?#[]@!$&\'()*+,;=]+';
    }

    public function getStringToTypeCastFunction(Schema $schema): string
    {
        return 'strval';
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
        return 'String';
    }

    public function getContentTypeChecking(Schema $schema): string
    {
        return 'is_string($content)';
    }

    public function getConstraints(Schema $schema): array
    {
        $constraints = [];

        if ($schema->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $schema->pattern]);
        }

        if ($schema->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $schema->minLength]);
        }

        if ($schema->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $schema->maxLength]);
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
        return 'string';
    }
}