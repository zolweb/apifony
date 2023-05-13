<?php

namespace App\Command;

class StringSchema extends Schema
{
    public readonly ?string $default;
    private readonly ?string $format;
    private readonly ?string $pattern;
    private readonly ?int $minLength;
    private readonly ?int $maxLength;

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
        return $this->default;
    }

    public function getConstraints(): array
    {
        return [];
        $constraints = [];

        if ($this->format !== null) {
            // $formatClasses = $this->generateFormatClasses($schema['format']);
            $formatClasses = ['constraintClassName' => 'Lol'];
            $constraints[] = sprintf(
                '%s()',
                $formatClasses['constraintClassName'],
            );
        }

        if ($this->pattern !== null) {
            $constraints[] = sprintf(
                'Assert\Regex(\'/%s/\')',
                $this->pattern,
            );
        }

        if ($this->minLength !== null) {
            $constraints[] = sprintf(
                'Assert\Length(min: %d)',
                $this->minLength,
            );
        }

        if ($this->maxLength !== null) {
            $constraints[] = sprintf(
                'Assert\Length(max: %d)',
                $this->maxLength,
            );
        }
    }

    public function getFiles(): array
    {
        return [];
    }
}