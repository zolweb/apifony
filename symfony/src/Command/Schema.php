<?php

namespace App\Command;

class Schema
{
    public readonly ?string $type;
    public readonly mixed $default;
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
    private readonly ?Schema $items;
    public readonly ?array $properties;

    public function __construct(
        private readonly ?string $name,
        private readonly bool $required,
        array $data,
    ) {
        if (isset($data['$ref'])) {
            $data = $parent->resolveReference($data['$ref']);
        }

        $this->type = $data['type'] ?? null;
        $this->default = $data['default'] ?? null;
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
        $this->items = isset($data['items']) ? new Schema($this, null, false, $data['items']) : null;
        $this->properties = isset($data['properties']) ?
            array_map(
                fn (string $name) => new Schema($this, $name, isset($data['required'][$name]), $data['properties'][$name]),
                array_keys($data['properties']),
            ) : null;
    }

    public function getClassName(): string
    {
        return uniqid();
    }

    public function getArrayProperties(): array
    {
        return array_filter(
            $this->properties,
            static fn (Schema $property) => $property->type === 'array',
        );
    }

    public function toMethodParameter(): string
    {
        return sprintf(
            '%s%s $%s%s',
            $this->required || $this->type === null ? '' : '?',
            match ($this->type ?? 'mixed') {
                'string' => 'string',
                'number' => 'float',
                'integer' => 'int',
                'boolean' => 'bool',
                'array' => 'array',
                'object' => 'Lol', // $this->toObjectSchemaClassName($property, ucfirst("{$propertyName}{$parentSchemaName}")),
                'mixed' => 'mixed',
            },
            $this->name,
            '', //($default = $this->getSchemaDefault($property)) !== null ? sprintf(' = %s', $default) : '',
        );
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

    public function toPhpDocArrayParamAnnotation(): string
    {
        return sprintf(
            '@param %sarray<%s> $%s',
            $this->required ? '' : '?',
            match ($this->items->type) {
                'string' => 'string',
                'number' => 'float',
                'integer' => 'int',
                'boolean' => 'bool',
                'array' => 'array',
                'object' => 'Lol', // $this->toObjectSchemaClassName($property['items'], ucfirst("{$propertyName}{$parentSchemaName}")),
            },
            $this->name,
        );
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

    public function getFiles(): array
    {
        return match ($this->type) {
            'object' => array_merge(
                [uniqid() => ['template' => 'schema.php.twig', 'params' => ['schema' => $this]]],
                ...array_map(
                    static fn (Schema $property) => $property->getFiles(),
                    $this->properties,
                ),
            ),
            'array' => $this->items->getFiles(),
            // 'string' => $this->format !== null ? [
            //     uniqid() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
            //     uniqid() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
            //     uniqid() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
            // ] : [],
            default => [],
        };
    }
}