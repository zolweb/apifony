<?php

namespace App\Command;

class BooleanSchema extends Schema
{
    private readonly ?bool $default;

    public function __construct(
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);

        $this->default = $data['default'] ?? null;
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
        return [true => 'true', false => 'false', null => null][$this->default];
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getFiles(): array
    {
        return [];
    }
}