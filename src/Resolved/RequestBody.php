<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\RequestBody.
 */
final class RequestBody
{
    /**
     * @param array<string, MediaType> $content
     * @param array<string, mixed>     $extensions
     * @param list<string>             $path
     */
    public function __construct(
        public readonly bool $required,
        public readonly array $content,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
