<?php

namespace App\Command\OpenApi;

class Paths
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        return new self(
            array_map(
                fn (string $route) => PathItem::build($route, $data[$route]),
                array_filter(
                    array_keys($data),
                    static fn (string $route) => $route[0] === '/',
                ),
            ),
        );
    }

    private function __construct(
        /** @var array<PathItem> */
        public readonly array $pathItems,
    ) {
    }
}