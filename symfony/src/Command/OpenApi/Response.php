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
            array_combine(
                $names = array_keys($data['headers'] ?? []),
                array_map(
                    fn (string $name) => isset($data['headers'][$name]['$ref']) ?
                        Reference::build($data['headers'][$name]) : Header::build($data['headers'][$name]),
                    $names,
                ),
            ),
            array_combine(
                $types = array_filter(
                    array_keys($data['content'] ?? []),
                    static fn (string $type) => in_array($type, ['application/json'], true),
                ),
                array_map(
                    fn (string $type) => MediaType::build($data['content'][$type]),
                    $types,
                ),
            ),
        );
    }

    /**
     * @param array<string, Reference|Header> $headers
     * @param array<string, MediaType> $content
     */
    private function __construct(
        public readonly array $headers,
        public readonly array $content,
    ) {
    }
}