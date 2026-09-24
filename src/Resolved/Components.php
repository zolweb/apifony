<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * The image of an OpenApi\Components. Every bucket keeps the order the document declares, which is
 * what the format collection walks.
 */
final class Components
{
    /**
     * @param array<string, Schema>      $schemas
     * @param array<string, Response>    $responses
     * @param array<string, Parameter>   $parameters
     * @param array<string, RequestBody> $requestBodies
     * @param array<string, Header>      $headers
     * @param array<string, mixed>       $extensions
     * @param list<string>               $path
     */
    public function __construct(
        public readonly array $schemas,
        public readonly array $responses,
        public readonly array $parameters,
        public readonly array $requestBodies,
        public readonly array $headers,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
