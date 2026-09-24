<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\PathItem.
 */
final class PathItem
{
    /**
     * @param list<Parameter>          $parameters
     * @param array<string, Operation> $operations
     * @param array<string, mixed>     $extensions
     * @param list<string>             $path
     */
    public function __construct(
        public readonly array $parameters,
        public readonly array $operations,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
