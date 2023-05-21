<?php

namespace App\Command\OpenApi;

class Response
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
                fn (string $name) => isset($data['headers'][$name]['$ref']) ?
                    Reference::build($data['headers'][$name]) : Header::build($data['headers'][$name]),
                array_keys($data['headers'] ?? []),
            ),
            array_map(
                fn (string $type) => MediaType::build($type, $data['content'][$type]),
                array_filter(
                    array_keys($data['content'] ?? []),
                    static fn (string $type) => in_array($type, ['application/json'], true),
                ),
            ),
        );
    }

    private function __construct(
        /** @var array<Reference|Header> */
        public readonly array $headers,
        /** @var array<MediaType> */
        public readonly array $content,
    ) {
    }
}