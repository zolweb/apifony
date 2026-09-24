<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Response. Its headers hold resolved Header objects: a header written as
 * a reference is indistinguishable from an inline one once resolved, since nothing downstream
 * names a class after a header.
 */
final class Response
{
    /**
     * @param array<string, Header>    $headers
     * @param array<string, MediaType> $content
     * @param array<string, mixed>     $extensions
     * @param list<string>             $path
     */
    public function __construct(
        public readonly array $headers,
        public readonly array $content,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
