<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

/**
 * @phpstan-type JsonSchemaType 'string'|'integer'|'number'|'boolean'|'array'|'object'|'null'
 */
class Schema
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        $type = null;
        if (\array_key_exists('type', $data)) {
            if (\is_array($data['type'])) {
                if (\count($data['type']) === 0) {
                    throw new Exception('Schema objects type attribute array must not be empty.', $path);
                }
                $type = [];
                foreach ($data['type'] as $t) {
                    if (!\in_array($t, ['string', 'integer', 'number', 'boolean', 'array', 'object', 'null'], true)) {
                        throw new Exception('Schema objects type attribute must be one of \'string\', \'integer\', \'number\', \'boolean\', \'array\', \'object\' or \'null\', or an array of some of these.', $path);
                    }
                    $type[] = $t;
                }
            } elseif (\in_array($data['type'], ['string', 'integer', 'number', 'boolean', 'array', 'object', 'null'], true)) {
                $type = $data['type'];
            } else {
                throw new Exception('Schema objects type attribute must be one of \'string\', \'integer\', \'number\', \'boolean\', \'array\', \'object\' or \'null\', or an array of some of these.', $path);
            }
        }
        if (\array_key_exists('format', $data) && !\is_string($data['format'])) {
            throw new Exception('Schema objects format attribute must be a string.', $path);
        }
        $enum = [];
        if (\array_key_exists('enum', $data)) {
            if (!\is_array($data['enum'])) {
                throw new Exception('Schema objects enum attribute must be a string.', $path);
            }
            $types = \is_string($type) ? [$type] : $type;
            foreach ($data['enum'] as $e) {
                switch (true) {
                    case \is_string($e):
                        if ($types !== null && !\in_array('string', $types, true)) {
                            throw new Exception('Schema objects enum attribute elements must be compatible with type attribute.', $path);
                        }
                        break;
                    case \is_int($e):
                        if ($types !== null && !\in_array('integer', $types, true) && !\in_array('number', $types, true)) {
                            throw new Exception('Schema objects enum attribute elements must be compatible with type attribute.', $path);
                        }
                        break;
                    case \is_float($e):
                        if ($types !== null && !\in_array('number', $types, true)) {
                            throw new Exception('Schema objects enum attribute elements must be compatible with type attribute.', $path);
                        }
                        break;
                    case \is_bool($e):
                        if ($types !== null && !\in_array('boolean', $types, true)) {
                            throw new Exception('Schema objects enum attribute elements must be compatible with type attribute.', $path);
                        }
                        break;
                    case $e === null:
                        if ($types !== null && !\in_array('null', $types, true)) {
                            throw new Exception('Schema objects enum attribute elements must be compatible with type attribute.', $path);
                        }
                        break;
                    default:
                        throw new Exception('Only Schema objects enum attribute elements must be a string, an int, a float, a boolean or null.', $path);
                }
                $enum[] = $e;
            }
        }
        if (\array_key_exists('default', $data) && !\is_string($data['default']) && !\is_int($data['default']) && !\is_float($data['default']) && !\is_bool($data['default']) && null !== $data['default']) {
            throw new Exception('Schema objects default attribute must be a string, an int, a float, a boolean or null.', $path);
        }
        if (\array_key_exists('pattern', $data) && !\is_string($data['pattern'])) {
            throw new Exception('Schema objects pattern attribute must be a string.', $path);
        }
        if (\array_key_exists('minLength', $data) && !\is_int($data['minLength'])) {
            throw new Exception('Schema objects minLength attribute must be an int.', $path);
        }
        if (\array_key_exists('maxLength', $data) && !\is_int($data['maxLength'])) {
            throw new Exception('Schema objects maxLength attribute must be an int.', $path);
        }
        if (\array_key_exists('multipleOf', $data) && !\is_int($data['multipleOf']) && !\is_float($data['multipleOf'])) {
            throw new Exception('Schema objects multipleOf attribute must be an int or a float.', $path);
        }
        if (\array_key_exists('minimum', $data) && !\is_int($data['minimum']) && !\is_float($data['minimum'])) {
            throw new Exception('Schema objects minimum attribute must be an int or a float.', $path);
        }
        if (\array_key_exists('maximum', $data) && !\is_int($data['maximum']) && !\is_float($data['maximum'])) {
            throw new Exception('Schema objects maximum attribute must be an int or a float.', $path);
        }
        if (\array_key_exists('exclusiveMinimum', $data) && !\is_int($data['exclusiveMinimum']) && !\is_float($data['exclusiveMinimum'])) {
            throw new Exception('Schema objects exclusiveMinimum attribute must be an int or a float.', $path);
        }
        if (\array_key_exists('exclusiveMaximum', $data) && !\is_int($data['exclusiveMaximum']) && !\is_float($data['exclusiveMaximum'])) {
            throw new Exception('Schema objects exclusiveMaximum attribute must be an int or a float.', $path);
        }
        if (\array_key_exists('items', $data) && !\is_array($data['items'])) {
            throw new Exception('Schema objects items attribute must be an array.', $path);
        }
        if (\array_key_exists('minItems', $data) && !\is_int($data['minItems'])) {
            throw new Exception('Schema objects minItems attribute must be an int.', $path);
        }
        if (\array_key_exists('maxItems', $data) && !\is_int($data['maxItems'])) {
            throw new Exception('Schema objects maxItems attribute must be an int.', $path);
        }
        if (\array_key_exists('uniqueItems', $data) && !\is_bool($data['uniqueItems'])) {
            throw new Exception('Schema objects uniqueItems attribute must be a boolean.', $path);
        }
        if (\array_key_exists('properties', $data) && !\is_array($data['properties'])) {
            throw new Exception('Schema objects properties attribute must be an array.', $path);
        }
        $properties = [];
        if (\array_key_exists('properties', $data)) {
            if (!\is_array($data['properties'])) {
                throw new Exception('Schema objects properties attribute must be an array.', $path);
            }
            foreach ($data['properties'] as $name => $propertyData) {
                if (!\is_array($propertyData)) {
                    throw new Exception('Schema objects properties attribute elements must be arrays.', $path);
                }
                if (!\is_string($name)) {
                    throw new Exception('Schema objects properties attribute keys must be strings.', $path);
                }
                $propertyPath = $path;
                $propertyPath[] = 'properties';
                $propertyPath[] = $name;
                $properties[$name] = \array_key_exists('$ref', $propertyData) ? Reference::build($propertyData, $propertyPath) : self::build($propertyData, $propertyPath);
            }
        }

        $required = [];
        if (\array_key_exists('required', $data)) {
            if (!\is_array($data['required'])) {
                throw new Exception('Schema objects required attribute must be a string.', $path);
            }
            foreach ($data['required'] as $e) {
                if (!\is_string($e)) {
                    throw new Exception('Schema objects required attribute elements must be a string.', $path);
                }
                $required[] = $e;
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $itemsPath = $path;
        $itemsPath[] = 'items';

        return new self(
            $type,
            $data['format'] ?? null,
            $enum,
            \array_key_exists('default', $data),
            $data['default'] ?? null,
            $data['pattern'] ?? null,
            $data['minLength'] ?? null,
            $data['maxLength'] ?? null,
            $data['multipleOf'] ?? null,
            $data['minimum'] ?? null,
            $data['maximum'] ?? null,
            $data['exclusiveMinimum'] ?? null,
            $data['exclusiveMaximum'] ?? null,
            match (true) {
                \array_key_exists('items', $data) && \array_key_exists('$ref', $data['items']) => Reference::build($data['items'], $itemsPath),
                \array_key_exists('items', $data) => self::build($data['items'], $itemsPath),
                default => null,
            },
            $data['minItems'] ?? null,
            $data['maxItems'] ?? null,
            $data['uniqueItems'] ?? false,
            $properties,
            $required,
            $extensions,
            $path,
        );
    }

    /**
     * @param JsonSchemaType|non-empty-list<JsonSchemaType>|null $type
     * @param list<string|int|float|bool|null>                   $enum
     * @param array<string, Reference|Schema>                    $properties
     * @param list<string>                                       $required
     * @param array<string, mixed>                               $extensions
     * @param list<string>                                       $path
     */
    private function __construct(
        public readonly string|array|null $type,
        public readonly ?string $format,
        public readonly array $enum,
        public readonly bool $hasDefault,
        public readonly string|int|float|bool|null $default,
        public readonly ?string $pattern,
        public readonly ?int $minLength,
        public readonly ?int $maxLength,
        public readonly int|float|null $multipleOf,
        public readonly int|float|null $minimum,
        public readonly int|float|null $maximum,
        public readonly int|float|null $exclusiveMinimum,
        public readonly int|float|null $exclusiveMaximum,
        public readonly Reference|self|null $items,
        public readonly ?int $minItems,
        public readonly ?int $maxItems,
        public readonly bool $uniqueItems,
        public readonly array $properties,
        public readonly array $required,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
