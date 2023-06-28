<?php

namespace Zol\Ogen\OpenApi;

class Reference
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        if (!isset($data['$ref'])) {
            throw new Exception('Reference object $ref attribute is mandatory.');
        }
        if (!is_string($data['$ref'])) {
            throw new Exception('Reference object $ref attribute must be a string.');
        }

        return new self($data['$ref']);
    }

    private function __construct(
        public readonly string $ref,
    ) {
    }

    public function getName(): string
    {
        // TODO check errors
        return explode('/', $this->ref)[3];
    }
}