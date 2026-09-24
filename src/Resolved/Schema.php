<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Schema once its two reference bearing slots, items and properties, hold
 * a SchemaRef rather than a raw union. Every other attribute is carried through unchanged, by
 * value, so that the generators keep reading them exactly as they did.
 *
 * The node carries no name of its own: a components schema is named by the SchemaRef pointing at
 * it and by the key it sits under in Components::$schemas. That keeps the node a faithful image of
 * what the specification declares, and it keeps $path meaning what it has always meant.
 *
 * @phpstan-import-type JsonSchemaType from \Zol\Apifony\OpenApi\Schema
 */
final class Schema
{
    /**
     * @param JsonSchemaType|non-empty-list<JsonSchemaType>|null $type
     * @param list<string|int|float|bool|array{}|null>           $enum
     * @param string|int|float|bool|array{}|null                 $default
     * @param array<string, SchemaRef>                           $properties
     * @param list<string>                                       $required
     * @param array<string, mixed>                               $extensions
     * @param list<string>                                       $path
     */
    public function __construct(
        public readonly string|array|null $type,
        public readonly ?string $format,
        public readonly array $enum,
        public readonly bool $hasDefault,
        public readonly string|int|float|bool|array|null $default,
        public readonly ?string $pattern,
        public readonly ?int $minLength,
        public readonly ?int $maxLength,
        public readonly int|float|null $multipleOf,
        public readonly int|float|null $minimum,
        public readonly int|float|null $maximum,
        public readonly int|float|null $exclusiveMinimum,
        public readonly int|float|null $exclusiveMaximum,
        public readonly ?SchemaRef $items,
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
