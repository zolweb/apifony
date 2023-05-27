<?php

namespace App\Command\OpenApi;

class Reference
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
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
            // TODO Be less strict here in OpenApi package, and do the check in Bundle package ?
            throw new Exception('Reference object $ref attribute with format not matching ^#/components/(schemas|responses|parameters|requestBodies|headers)/[a-zA-Z0-9.\-_]+$ as not supported');
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