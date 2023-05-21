<?php

namespace App\Command\OpenApi;

use function Symfony\Component\String\u;

class Schema
{
    public readonly string $className;
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
    public readonly null|Reference|Schema $items;
    public readonly ?int $minItems;
    public readonly ?int $maxItems;
    public readonly bool $uniqueItems;
    /** @var null|array<string, Reference|Schema> */
    public readonly ?array $properties;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array $data): self
    {
        $schema = new self();

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
        $schema->items = match (true) {
            isset($data['items']['$ref']) => Reference::build($data['items']),
            isset($data['items']) => Schema::build("{$className}List", $data['items']),
            default => null,
        };
        $schema->properties = isset($data['properties']) ?
            array_combine(
                $keys = array_keys($data['properties']),
                array_map(
                    fn (string $name) => isset($data['properties'][$name]['$ref']) ?
                        Reference::build($data['properties'][$name]) :
                        Schema::build(
                            sprintf('%s%s', $className, u($name)->camel()->title()),
                            $data['properties'][$name],
                        ),
                    $keys,
                ),
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

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        $constraints = $this->type->getConstraints();

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->format !== null) {
            $constraints[] = new Constraint(sprintf('AssertFormat\%s', u($this->format)->camel()->title()), []);
        }

        return $constraints;
    }

    public function addFiles(array& $files, string $folder): void
    {
        $this->type->addFiles($files, $folder);

        $baseName = u($this->format)->camel()->title();
        $definitionInterfaceName = "{$baseName}Definition";
        $constraintClassName = "{$baseName}";
        $validatorClassName = "{$baseName}Validator";

        if ($this->format !== null && !isset($files["src/Format/{$definitionInterfaceName}.php"])) {
            $files["src/Format/{$definitionInterfaceName}.php"] = [
                'folder' => 'src/Format',
                'name' => "{$definitionInterfaceName}.php",
                'template' => 'format-definition.php.twig',
                'params' => [
                    'schema' => $this,
                    'definitionInterfaceName' => $definitionInterfaceName,
                ],
            ];
            $files["src/Format/{$constraintClassName}.php"] = [
                'folder' => 'src/Format',
                'name' => "{$constraintClassName}.php",
                'template' => 'format-constraint.php.twig',
                'params' => [
                    'schema' => $this,
                    'constraintClassName' => $constraintClassName,
                ],
            ];
            $files["src/Format/{$validatorClassName}.php"] = [
                'folder' => 'src/Format',
                'name' => "{$validatorClassName}.php",
                'template' => 'format-validator.php.twig',
                'params' => [
                    'schema' => $this,
                    'validatorClassName' => $validatorClassName,
                    'definitionInterfaceName' => $definitionInterfaceName,
                ],
            ];
        }
    }
}