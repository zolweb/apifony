<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Parameter. A parameter can be written as a reference, but the generators
 * never ask whether it was, so the slots holding one carry the parameter itself.
 */
final class Parameter
{
    /**
     * @param array<mixed> $extensions
     * @param list<string> $path
     */
    public function __construct(
        public readonly string $name,
        public readonly string $in,
        public readonly bool $required,
        public readonly ?SchemaRef $schema,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
