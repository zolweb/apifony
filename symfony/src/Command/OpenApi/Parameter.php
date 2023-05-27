<?php

namespace App\Command\OpenApi;

class Parameter
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        if (!is_array($data)) {
            throw new Exception('Parameter object must be an array.');
        }
        if (!isset($data['name'])) {
            throw new Exception('Parameter object name attribute is mandatory.');
        }
        if (!is_string($data['name'])) {
            throw new Exception('Parameter object name attribute must be a string.');
        }
        if (!isset($data['in'])) {
            throw new Exception('Parameter object in attribute is mandatory.');
        }
        if (!is_string($data['in'])) {
            throw new Exception('Parameter object in attribute must be a string.');
        }
        if (isset($data['required']) && !is_bool($data['required'])) {
            throw new Exception('Parameter object required attribute must be a boolean.');
        }

        return new self(
            $data['name'],
            $data['in'],
            $data['required'] ?? false,
            // todo schema can be null
            isset($data['schema']['$ref']) ?
                Reference::build($data['schema']) : Schema::build($data['schema']),
        );
    }

    private function __construct(
        public readonly string $name,
        public readonly string $in,
        public readonly bool $required,
        public readonly null|Reference|Schema $schema,
    ) {
    }
}