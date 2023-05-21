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
            array_combine(
                $routes = array_filter(
                    array_keys($data),
                    static fn (string $route) => $route[0] === '/',
                ),
                array_map(
                    fn (string $route) => PathItem::build($data[$route]),
                    $routes,
                ),
            )
        );
    }

    private function __construct(
        /** @var array<string, PathItem> */
        public readonly array $pathItems,
    ) {
    }
}