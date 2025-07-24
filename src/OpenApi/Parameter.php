<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Parameter
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        if (!isset($data['name'])) {
            throw new Exception('Parameter object name attribute is mandatory.', $path);
        }
        if (!\is_string($data['name'])) {
            throw new Exception('Parameter object name attribute must be a string.', $path);
        }
        if (!isset($data['in'])) {
            throw new Exception('Parameter object in attribute is mandatory.', $path);
        }
        if (!\is_string($data['in'])) {
            throw new Exception('Parameter object in attribute must be a string.', $path);
        }
        if (isset($data['required']) && !\is_bool($data['required'])) {
            throw new Exception('Parameter object required attribute must be a boolean.', $path);
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $schemaPath = $path;
        $schemaPath[] = 'schema';

        return new self(
            $data['name'],
            $data['in'],
            $data['required'] ?? false,
            match (true) {
                isset($data['schema']['$ref']) => Reference::build($data['schema'], $schemaPath),
                isset($data['schema']) => Schema::build($data['schema'], $schemaPath),
                default => null,
            },
            $extensions,
            $path,
        );
    }

    /**
     * @param array<mixed> $extensions
     * @param list<string> $path
     */
    private function __construct(
        public readonly string $name,
        public readonly string $in,
        public readonly bool $required,
        public readonly Reference|Schema|null $schema,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
