<?php

namespace App\Command\OpenApi;

use RuntimeException;

class Reference
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data, ?Components $components): self
    {
        if (!is_array($data)) {
            throw new Exception('Reference objects must be arrays.');
        }
        if (!isset($data['$ref'])) {
            throw new Exception('Reference object $ref attribute is mandatory.');
        }
        if (!is_string($data['$ref'])) {
            throw new Exception('Reference object $ref attribute must be a string.');
        }
        if (preg_match('%^#/components/(schemas|responses|parameters|requestBodies|headers)/[a-zA-Z0-9.\-_]+$%', $data['ref']) !== false) {
            throw new Exception('Reference object $ref attribute with format not matching ^#/components/(schemas|responses|parameters|requestBodies|headers)/[a-zA-Z0-9.\-_]+$ as not supported');
        }
        if (is_null($components)) {
            throw new Exception('Reference object $ref attribute must point to an existing component.');
        }

        [,, $type, $name] = explode('/', $data['$ref']);
        $typedComponents = match ($type) {
            'schemas' => $components->schemas,
            'responses' => $components->responses,
            'parameters' => $components->parameters,
            'requestBodies' => $components->requestBodies,
            'headers' => $components->headers,
            default => throw new RuntimeException(),
        };
        if (!isset($typedComponents[$name])) {
            throw new Exception('Reference object $ref attribute must point to an existing component.');
        }

        return new self($data['$ref']);
    }

    private function __construct(
        public readonly string $ref,
    ) {
    }

    public function getName(): string
    {
        return explode('/', $this->ref)[3];
    }
}