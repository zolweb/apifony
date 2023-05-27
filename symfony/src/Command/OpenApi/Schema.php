<?php

namespace App\Command\OpenApi;

class Schema
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $type = [];
        if (isset($data['type'])) {
            if (is_array($data['type'])) {
                foreach ($data['type'] as $t) {
                    if (!is_string($t)) {
                        throw new Exception('Schema objects type attribute must be a string or an array of strings.');
                    }
                    $type[] = $type;
                }
            } if (is_string($data['type'])) {
                $type = $data['type'];
            } else {
                throw new Exception('Schema objects type attribute must be a string or an array of strings.');
            }
        }
        if (isset($data['format']) && !is_string($data['format'])) {
            throw new Exception('Schema objects format attribute must be a string.');
        }
        $enum = [];
        if (isset($data['enum'])) {
            if (!is_array($data['enum'])) {
                throw new Exception('Schema objects enum attribute must be a string.');
            }
            foreach ($data['enum'] as $e) {
                if (!is_string($e) && !is_int($e) && !is_float($e) && !is_bool($e)) {
                    throw new Exception('Schema objects enum attribute elements must be a string, an int, a float or a boolean.');
                }
                $enum[] = $e;
            }
        }
        if (isset($data['default']) && !is_string($data['default']) && !is_int($data['default']) && !is_float($data['default']) && !is_bool($data['default'])) {
            throw new Exception('Schema objects default attribute must be a string, an int, a float or a boolean.');
        }
        if (isset($data['pattern']) && !is_string($data['pattern'])) {
            throw new Exception('Schema objects pattern attribute must be a string.');
        }
        if (isset($data['minLength']) && !is_int($data['minLength'])) {
            throw new Exception('Schema objects minLength attribute must be an int.');
        }
        if (isset($data['maxLength']) && !is_int($data['maxLength'])) {
            throw new Exception('Schema objects maxLength attribute must be an int.');
        }
        if (isset($data['multipleOf']) && !is_int($data['multipleOf']) && !is_float($data['multipleOf'])) {
            throw new Exception('Schema objects multipleOf attribute must be an int or a float.');
        }
        if (isset($data['minimum']) && !is_int($data['minimum']) && !is_float($data['minimum'])) {
            throw new Exception('Schema objects minimum attribute must be an int or a float.');
        }
        if (isset($data['maximum']) && !is_int($data['maximum']) && !is_float($data['maximum'])) {
            throw new Exception('Schema objects maximum attribute must be an int or a float.');
        }
        if (isset($data['exclusiveMinimum']) && !is_int($data['exclusiveMinimum']) && !is_float($data['exclusiveMinimum'])) {
            throw new Exception('Schema objects exclusiveMinimum attribute must be an int or a float.');
        }
        if (isset($data['exclusiveMaximum']) && !is_int($data['exclusiveMaximum']) && !is_float($data['exclusiveMaximum'])) {
            throw new Exception('Schema objects exclusiveMaximum attribute must be an int or a float.');
        }
        if (isset($data['items']) and !is_array($data['items'])) {
            throw new Exception('Schema objects items attribute must be an array.');
        }
        if (isset($data['minItems']) && !is_int($data['minItems'])) {
            throw new Exception('Schema objects minItems attribute must be an int.');
        }
        if (isset($data['maxItems']) && !is_int($data['maxItems'])) {
            throw new Exception('Schema objects maxItems attribute must be an int.');
        }
        if (isset($data['uniqueItems']) && !is_bool($data['uniqueItems'])) {
            throw new Exception('Schema objects uniqueItems attribute must be a boolean.');
        }
        if (isset($data['properties']) && !is_array($data['properties'])) {
            throw new Exception('Schema objects properties attribute must be an array.');
        }
        $properties = [];
        if (isset($data['properties'])) {
            if (!is_array($data['properties'])) {
                throw new Exception('Schema objects properties attribute must be an array.');
            }
            foreach ($data['properties'] as $name => $propertyData) {
                if (!is_array($propertyData)) {
                    throw new Exception('Schema objects properties attribute elements must be arrays.');
                }
                if (!is_string($name)) {
                    throw new Exception('Schema objects properties attribute keys must be strings.');
                }
                $properties[$name] = isset($propertyData['$ref']) ? Reference::build($propertyData) : Schema::build($propertyData);
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self(
            $type,
            $data['format'] ?? null,
            $enum,
            isset($data['default']),
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
                isset($data['items']['$ref']) => Reference::build($data['items']),
                isset($data['items']) => Schema::build($data['items']),
                default => null,
            },
            $data['minItems'] ?? null,
            $data['maxItems'] ?? null,
            $data['uniqueItems'] ?? false,
            $properties,
            $extensions,
        );
    }

    /**
     * @param string|array<string> $type
     * @param array<string|int|float|bool> $enum
     * @param array<string, Reference|Schema> $properties
     * @param array<string, mixed> $extensions
     */
    private function __construct(
        public readonly string|array $type,
        public readonly ?string $format,
        public readonly array $enum,
        public readonly bool $hasDefault,
        public readonly null|string|int|float|bool $default,
        public readonly ?string $pattern,
        public readonly ?int $minLength,
        public readonly ?int $maxLength,
        public readonly null|int|float $multipleOf,
        public readonly null|int|float $minimum,
        public readonly null|int|float $maximum,
        public readonly null|int|float $exclusiveMinimum,
        public readonly null|int|float $exclusiveMaximum,
        public readonly null|Reference|Schema $items,
        public readonly ?int $minItems,
        public readonly ?int $maxItems,
        public readonly bool $uniqueItems,
        public readonly array $properties,
        public readonly array $extensions,
    ) {
    }
}