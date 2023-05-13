<?php

namespace App\Command;

class StringSchema extends Schema
{
    private readonly ?string $default;
    private readonly ?string $format;
    private readonly ?string $pattern;
    private readonly ?int $minLength;
    private readonly ?int $maxLength;
    private readonly ?array $enum;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);

        $this->default = $data['default'] ?? null;
        $this->format = $data['format'] ?? null;
        $this->pattern = $data['pattern'] ?? null;
        $this->minLength = $data['minLength'] ?? null;
        $this->maxLength = $data['maxLength'] ?? null;
        $this->enum = $data['enum'] ?? null;
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
        return sprintf('\'%s\'', str_replace('\'', '\\\'', $this->default));
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint($this->format, []);
        }

        if ($this->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $this->pattern]);
        }

        if ($this->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $this->minLength]);
        }

        if ($this->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $this->maxLength]);
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