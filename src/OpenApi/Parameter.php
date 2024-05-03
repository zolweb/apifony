<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

class Parameter
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        if (!isset($data['name'])) {
            throw new Exception('Parameter object name attribute is mandatory.');
        }
        if (!\is_string($data['name'])) {
            throw new Exception('Parameter object name attribute must be a string.');
        }
        if (!isset($data['in'])) {
            throw new Exception('Parameter object in attribute is mandatory.');
        }
        if (!\is_string($data['in'])) {
            throw new Exception('Parameter object in attribute must be a string.');
        }
        if (isset($data['required']) && !\is_bool($data['required'])) {
            throw new Exception('Parameter object required attribute must be a boolean.');
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self(
            $data['name'],
            $data['in'],
            $data['required'] ?? false,
            match (true) {
                isset($data['schema']['$ref']) => Reference::build($data['schema']),
                isset($data['schema']) => Schema::build($data['schema']),
                default => null,
            },
            $extensions,
        );
    }

    /**
     * @param array<mixed> $extensions
     */
    private function __construct(
        public readonly string $name,
        public readonly string $in,
        public readonly bool $required,
        public readonly Reference|Schema|null $schema,
        public readonly array $extensions,
    ) {
    }
}
