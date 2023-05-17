<?php

namespace App\Command;

class BooleanType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'bool';
    }

    public function getMethodParameterType(): string
    {
        return 'bool';
    }

    public function getMethodParameterDefault(): ?string
    {
        return [true => 'true', false => 'false', null => null][$this->schema->default];
    }

    public function getRouteRequirementPattern(): string
    {
        return 'true|false';
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'boolval';
    }

    public function getRequestBodyPayloadInitializationFromRequest(): string
    {
        return '$requestBodyPayload = json_decode($request->getContent(), true);';
    }

    public function getRequestBodyPayloadValidationViolationsInitialization(): string
    {
        return sprintf(
            "\$violations = \$validator->validate(\$requestBodyPayload, [\n%s\n]);",
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
        return 'Boolean';
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return 'is_bool($requestBodyPayload)';
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->schema->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }

    public function addFiles(array& $files): void
    {
    }

    public function __toString(): string
    {
        return 'boolean';
    }
}