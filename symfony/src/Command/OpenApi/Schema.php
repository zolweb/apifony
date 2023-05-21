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

        return new self(
            $type,
            $nullable,
            $data['format'] ?? null,
            $data['enum'] ?? null,
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
            isset($data['properties']) ?
                array_combine(
                    $keys = array_keys($data['properties']),
                    array_map(
                        fn (string $name) => isset($data['properties'][$name]['$ref']) ?
                            Reference::build($data['properties'][$name]) : Schema::build($data['properties'][$name]),
                        $keys,
                    ),
                ) : null,
        );
    }

    private function __construct(
        public readonly string $type,
        public readonly bool $nullable,
        public readonly ?string $format,
        /** @var null|array<string|int|float|bool> */
        public readonly ?array $enum,
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
        /** @var null|array<string, Reference|Schema> */
        public readonly ?array $properties,
    ) {
    }
}