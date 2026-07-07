<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class MediaType
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        $schema = null;
        $schemaRef = null;
        if (isset($data['schema'])) {
            if (!\is_array($data['schema'])) {
                throw new Exception('MediaType object schema attribute must be an array.', $path);
            }
            $schema = $data['schema'];
            if (isset($data['schema']['$ref'])) {
                if (!\is_string($data['schema']['$ref'])) {
                    throw new Exception('MediaType object schema attribute $ref attribute must be a string.', $path);
                }
                $schemaRef = $data['schema']['$ref'];
            }
        }

        $schemaPath = $path;
        $schemaPath[] = 'schema';

        return new self(
            match (true) {
                $schema !== null && $schemaRef !== null => Reference::build($schema, $schemaPath),
                $schema !== null => Schema::build($schema, $schemaPath),
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
