<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Schema
{
    public readonly MediaType|Parameter|Schema|Header $parent;
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
    public readonly Schema $items;
    public readonly ?int $minItems;
    public readonly ?int $maxItems;
    public readonly bool $uniqueItems;
    /** @var null|array<Schema> */
    public readonly ?array $properties;

    /**
     * @throws Exception
     */
    public static function build(
        MediaType|Parameter|Schema|Header $parent,
        array $componentsData,
        array $data,
    ): self {
        $schema = new self();

        $schemaName = null;
        if (isset($data['$ref'])) {
            [, , $type, $schemaName] = explode('/', $data['$ref']);
            $data = $componentsData[$type][$schemaName];
        }

        if (!isset($data['type'])) {
            throw new Exception('Schemas without type are not supported.');
        }

        if (isset($data['items']['type']) && $data['items']['type'] === 'array') {
            throw new Exception('Array schemas of arrays are not supported.');
        }

        $nullable = false;
        if ($data['type'] === 'null') {
            throw new Exception('Null schemas are not supported.');
        } if (is_array($data['type'])) {
            if (count($data['type']) > 2 || !in_array('null', $data['type'], true)) {
                throw new Exception('Schemas with multiple types (but \'null\') are not supported.');
            }
            $nullable = true;
            $type = $data['type'][(int)($data['type'][0] === 'null')];
        } else {
            $type = $data['type'];
        }

        $schema->parent = $parent;
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
        $schema->items = isset($data['items']) ? Schema::build($parent, $componentsData, $data['items']) : null;
        $schema->properties = isset($data['properties']) ? array_map(
            fn (string $name) => Schema::build(
                $parent,
                $componentsData,
                $data['properties'][$name],
            ),
            array_keys($data['properties']),
        ) : null;

        $schema->type = match ($type) {
            'string' => new StringType(),
            'integer' => new IntegerType(),
            'number' => new NumberType(),
            'boolean' => new BooleanType(),
            'array' => new ArrayType(),
            'object' => new ObjectType(),
        };

        return $schema;
    }

    private function __construct()
    {
    }

    public function getPhpDocParameterAnnotation(string $variableName): string
    {
        return sprintf(
            '@param %s%s $%s',
            $this->nullable ? '' : '?',
            $this->getPhpDocParameterAnnotationType(),
            $variableName,
        );
    }

    public function getMethodParameter(string $variableName): string
    {
        return sprintf(
            '%s%s $%s%s',
            $this->nullable ? '' : '?',
            $this->getMethodParameterType(),
            $variableName,
            $this->getMethodParameterDefault() !== null ? sprintf(
                ' = %s',
                $this->getMethodParameterDefault(),
            ) : '',
        );
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return $this->type->getPhpDocParameterAnnotationType($this);
    }

    public function getMethodParameterType(): string
    {
        return $this->type->getMethodParameterType($this);
    }

    public function getMethodParameterDefault(): ?string
    {
        return $this->type->getMethodParameterDefault($this);
    }

    public function getRouteRequirement(string $parameterName): string
    {
        return sprintf(
            '\'%s\' => \'%s\',',
            $parameterName,
            str_replace('\'', '\\\'', $this->type->getRouteRequirementPattern($this)),
        );
    }

    public function getStringToTypeCastFunction(): string
    {
        return $this->type->getStringToTypeCastFunction($this);
    }

    public function getContentInitializationFromRequest(): string
    {
        return $this->type->getContentInitializationFromRequest($this);
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return $this->type->getContentValidationViolationsInitialization($this);
    }

    public function getNormalizedType(): string
    {
        return $this->type->getNormalizedType($this);
    }

    public function getContentTypeChecking(): string
    {
        return $this->type->getContentTypeChecking($this);
    }

    public function getConstraints(): array
    {
        $constraints = $this->type->getConstraints($this);

        if ($this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint($this->getNormalizedFormat(), []);
        }

        return $constraints;
    }

    public function getFiles(): array
    {
        return array_merge(
            $this->type->getFiles($this),
            $this->format !== null ?
                [
                    $this->getFormatDefinitionInterfaceName() => ['template' => 'format-definition.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatConstraintClassName() => ['template' => 'format-constraint.php.twig', 'params' => ['schema' => $this]],
                    $this->getFormatValidatorClassName() => ['template' => 'format-validator.php.twig', 'params' => ['schema' => $this]],
                ] : [],
        );
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
}