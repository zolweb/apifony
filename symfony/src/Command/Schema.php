<?php

namespace App\Command;

class Schema
{
    public readonly ?string $type;
    public readonly mixed $default;
    private readonly bool $required;
    private readonly ?string $format;
    private readonly ?string $pattern;
    private readonly ?int $minLength;
    private readonly ?int $maxLength;
    private readonly null|int|float $minimum;
    private readonly null|int|float $maximum;
    private readonly bool $exclusiveMinimum;
    private readonly bool $exclusiveMaximum;
    private readonly ?array $enum;
    private readonly ?array $minItems;
    private readonly ?array $maxItems;
    private readonly bool $uniqueItems;

    public function __construct(
        array $data,
    ) {
        $this->type = $data['type'] ?? null;
        $this->default = $data['default'] ?? null;
        $this->required = $data['required'] ?? false;
        $this->format = $data['format'] ?? null;
        $this->pattern = $data['pattern'] ?? null;
        $this->minLength = $data['minLength'] ?? null;
        $this->maxLength = $data['maxLength'] ?? null;
        $this->minimum = $data['minimum'] ?? null;
        $this->maximum = $data['maximum'] ?? null;
        $this->exclusiveMinimum = $data['exclusiveMinimum'] ?? false;
        $this->exclusiveMaximum = $data['exclusiveMaximum'] ?? false;
        $this->enum = $data['enum'] ?? null;
        $this->minItems = $data['exclusiveMaximum'] ?? null;
        $this->maxItems = $data['exclusiveMaximum'] ?? null;
        $this->uniqueItems = $data['uniqueItems'] ?? false;
    }

    public function getDefaultAsMethodParameterDefault(): ?string
    {
        if (isset($this->type) && isset($this->default)) {
            return match ($this->type) {
                'string' => sprintf('\'%s\'', str_replace('\'', '\\\'', $this->default)),
                'boolean' => $this->default ? 'true' : 'false',
                default => $this->default,
            };
        }

        return null;
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = 'Assert\NotNull()';
        }

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

        if ($this->minimum !== null) {
            $constraints[] = sprintf(
                'Assert\%s(%d)',
                    $this->exclusiveMinimum ?? false ? 'GreaterThan' : 'GreaterThanOrEqual',
                $this->minimum,
            );
        }

        if ($this->maximum !== null) {
            $constraints[] = sprintf(
                'Assert\%s(%d)',
                    $this->exclusiveMaximum ?? false ? 'LessThan' : 'LessThanOrEqual',
                $this->maximum,
            );
        }

        if ($this->enum !== null) {
            $constraints[] = sprintf(
                'Assert\Choice([\'%s\'])',
                implode('\', \'', $this->enum),
            );
        }

        if ($this->minItems !== null) {
            $constraints[] = sprintf(
                'Assert\Count(min: %d)',
                $this->minItems,
            );
        }

        if ($this->maxItems) {
            $constraints[] = sprintf(
                'Assert\Count(max: %d)',
                $this->maxItems,
            );
        }

        if ($this->uniqueItems) {
            $constraints[] = 'Assert\Unique()';
        }

        if ($this->type === 'object') {
            $constraints[] = 'Assert\Valid()';
        }

        // if ($this->type === 'array') {
        //     $constraints[] = sprintf(
        //         "Assert\All([%s\n\t\t])",
        //         implode(
        //             '',
        //             array_map(
        //                 static fn (string $c) => "\n\t\t\tnew $c,",
        //                 $this->getConstraints($required, $schema['items'] ?? []),
        //             ),
        //         ),
        //     );
        // }

        return $constraints;
    }
}