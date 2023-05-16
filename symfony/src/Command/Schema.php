<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Schema
{
    public readonly string $className;
    public readonly ?string $name;
    public readonly Type $type;
    public readonly bool $nullable;
    public readonly ?string $format;
    /** @var null|array<string|int|float|bool> */
    public readonly ?array $enum;
    public readonly null|string|int|float|bool $default;
    public readonly ?string $pattern;
    public readonly ?int $minLength;
    public readonly ?int $maxLength;
    public readonly null|int|float $multipleOf;
    public readonly null|int|float $minimum;
    public readonly null|int|float $maximum;
    public readonly null|int|float $exclusiveMinimum;
    public readonly null|int|float $exclusiveMaximum;
    public readonly ?Schema $items;
    public readonly ?int $minItems;
    public readonly ?int $maxItems;
    public readonly bool $uniqueItems;
    /** @var null|array<Schema> */
    public readonly ?array $properties;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array& $components, array $data, ?string $name = null): self
    {
        $schema = new self();

        if (isset($data['$ref'])) {
            $className = explode('/', $data['$ref'])[3];
            $component = &$components['schemas'][$className];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $schema;
                $data = $component['data'];
            }
        }

        if (!isset($data['type'])) {
            throw new Exception('Schemas without type are not supported.');
        }

        if (isset($data['items']['type']) && $data['items']['type'] === 'array') {
            throw new Exception('Array schemas of arrays are not supported.');
        }

        $nullable = false;
        if (is_array($data['type'])) {
            if (count($data['type']) === 1) {
                $type = $data['type'][0];
            } else {
                if (count($data['type']) > 2 || !in_array('null', $data['type'], true)) {
                    throw new Exception('Schemas with multiple types (but \'null\') are not supported.');
                }
                $nullable = true;
                $type = $data['type'][(int)($data['type'][0] === 'null')];
            }
        } else {
            $type = $data['type'];
        }

        if ($type === 'null') {
            throw new Exception('Null schemas are not supported.');
        }

        $schema->className = $className;
        $schema->name = $name;
        $schema->nullable = $nullable;
        $schema->format = $data['format'] ?? null;
        $schema->enum = $data['enum'] ?? null;
        $schema->default = $data['default'] ?? null;
        $schema->pattern = $data['pattern'] ?? null;
        $schema->minLength = $data['minLength'] ?? null;
        $schema->maxLength = $data['maxLength'] ?? null;
        $schema->multipleOf = $data['multipleOf'] ?? null;
        $schema->minimum = $data['minimum'] ?? null;
        $schema->maximum = $data['maximum'] ?? null;
        $schema->exclusiveMinimum = $data['exclusiveMinimum'] ?? null;
        $schema->exclusiveMaximum = $data['exclusiveMaximum'] ?? null;
        $schema->minItems = $data['minItems'] ?? null;
        $schema->maxItems = $data['maxItems'] ?? null;
        $schema->uniqueItems = $data['uniqueItems'] ?? false;
        $schema->items = isset($data['items']) ?
            Schema::build("{$className}List", $components, $data['items']) : null;
        $schema->properties = isset($data['properties']) ? array_map(
            fn (string $name) => Schema::build(
                sprintf('%s%s', $className, u($name)->camel()->title()),
                $components,
                $data['properties'][$name],
                $name,
            ),
            array_keys($data['properties']),
        ) : null;

        $schema->type = match ($type) {
            'string' => new StringType($schema),
            'integer' => new IntegerType($schema),
            'number' => new NumberType($schema),
            'boolean' => new BooleanType($schema),
            'array' => new ArrayType($schema),
            'object' => new ObjectType($schema),
        };

        return $schema;
    }

    private function __construct()
    {
    }

    public function getFormatDefinitionInterfaceName(): string
    {
        return "{$this->getNormalizedFormat()}Definition";
    }

    public function getFormatConstraintClassName(): string
    {
        return $this->getNormalizedFormat();
    }

    public function getFormatValidatorClassName(): string
    {
        return "{$this->getNormalizedFormat()}Validator";
    }

    public function getNormalizedFormat(): string
    {
        return u($this->format)->camel()->title();
    }

    public function getPhpDocParameterAnnotation(): string
    {
        return sprintf(
            '@param %s%s $%s',
            $this->nullable ? '' : '?',
            $this->type->getPhpDocParameterAnnotationType(),
            $this->name,
        );
    }

    public function getMethodParameter(): string
    {
        return sprintf(
            '%s%s $%s%s',
            $this->nullable ? '' : '?',
            $this->type->getMethodParameterType(),
            $this->name,
            $this->type->getMethodParameterDefault() !== null ? sprintf(
                ' = %s',
                $this->type->getMethodParameterDefault(),
            ) : '',
        );
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        $constraints = $this->type->getConstraints();

        if ($this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint($this->getNormalizedFormat(), []);
        }

        return $constraints;
    }

    public function addFiles(array& $files): void
    {
        $this->type->addFiles($files);

        if ($this->format !== null && !isset($files[$this->getFormatDefinitionInterfaceName()])) {
            $files[$this->getFormatDefinitionInterfaceName()] = ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]];
            $files[$this->getFormatConstraintClassName()] = ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]];
            $files[$this->getFormatValidatorClassName()] = ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]];
        }
    }
}