<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class StringType implements Type
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
        return 'string';
    }

    public function getMethodParameterType(): string
    {
        return 'string';
    }

    /**
     * @throws Exception
     */
    public function getMethodParameterDefault(): ?string
    {
        if ($this->schema->hasDefault) {
            if (is_string($this->schema->default)) {
                return sprintf('\'%s\'', str_replace('\'', '\\\'', $this->schema->default));
            }
            if (is_null($this->schema->default)) {
                if (!$this->nullable) {
                    throw new Exception('Schemas that are not nullable cannot have null default.');
                }
                return 'null';
            }
        }

        return null;
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

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        if ($this->schema->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $this->schema->pattern]);
        }

        if ($this->schema->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $this->schema->minLength]);
        }

        if ($this->schema->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $this->schema->maxLength]);
        }

        if (count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }
}