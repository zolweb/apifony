<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Responses.
 */
final class Responses
{
    /**
     * @param array<string|int, Response> $responses
     * @param array<string, mixed>        $extensions
     * @param list<string>                $path
     */
    public function __construct(
        public readonly array $responses,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
