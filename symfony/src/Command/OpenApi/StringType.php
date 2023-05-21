<?php

namespace App\Command\OpenApi;

class StringType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'string';
    }

    public function getMethodParameterType(): string
    {
        return 'string';
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->schema->default !== null ?
            sprintf('\'%s\'', str_replace('\'', '\\\'', $this->schema->default)) : null;
    }

    public function getRouteRequirementPattern(): string
    {
        return $this->schema->pattern !== null ? $this->schema->pattern : '[^:/?#[]@!$&\'()*+,;=]+';
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'strval';
    }

    public function getRequestBodyPayloadInitializationFromRequest(): string
    {
        return '$requestBodyPayload = json_decode($request->getContent(), true);';
    }

    public function getRequestBodyPayloadValidationViolationsInitialization(): string
    {
        return sprintf(
            "\$violations = \$this->validator->validate(\$requestBodyPayload, [\n%s\n]);",
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
        return 'String';
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return 'is_string($requestBodyPayload)';
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->schema->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $this->schema->pattern]);
        }

        if ($this->schema->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $this->schema->minLength]);
        }

        if ($this->schema->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $this->schema->maxLength]);
        }

        if ($this->schema->enum !== null) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }

    public function addFiles(array& $files, string $folder): void
    {
    }

    public function __toString(): string
    {
        return 'string';
    }
}