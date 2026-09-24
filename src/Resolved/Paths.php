<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Paths.
 */
final class Paths
{
    /**
     * @param array<string, PathItem> $pathItems
     * @param array<string, mixed>    $extensions
     * @param list<string>            $path
     */
    public function __construct(
        public readonly array $pathItems,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
