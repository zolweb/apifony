<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Schema;
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
        // The # character is written in the escaped sequence \x{0023} in order to prevent missing escape in regex at
        // line 180 in Symfony\Component\Routing\Generator\UrlGenerator.
        return $this->schema->pattern !== null ? $this->schema->pattern : '[^:/?\x{0023}[\\]@!$&\'\'()*+,;=]+'; // TODO Remove one of the \' and write a twig escaper for yaml strings
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'strval';
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

    public function getBuiltInPhpType(): string
    {
        return 'string';
    }

    public function getInitValue(): string
    {
        return '\'\'';
    }

    public function getUsedModel(): ?string
    {
        return null;
    }
}