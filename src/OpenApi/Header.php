<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Header
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        if (isset($data['schema']) && !\is_array($data['schema'])) {
            throw new Exception('Header object schema attribute must be an array.', $path);
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
        public readonly Reference|Schema|null $schema,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
