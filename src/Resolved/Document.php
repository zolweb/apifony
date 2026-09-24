<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * A whole specification with every '$ref' resolved: the single value the generation layer consumes,
 * in place of an OpenApi object plus the Components needed to make sense of it.
 */
final class Document
{
    /**
     * @param array<string, mixed> $extensions
     * @param list<string>         $path
     */
    public function __construct(
        public readonly ?Components $components,
        public readonly ?Paths $paths,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
