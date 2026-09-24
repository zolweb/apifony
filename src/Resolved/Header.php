<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Header.
 */
final class Header
{
    /**
     * @param array<mixed> $extensions
     * @param list<string> $path
     */
    public function __construct(
        public readonly ?SchemaRef $schema,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
