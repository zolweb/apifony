<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Reference
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        if (!isset($data['$ref'])) {
            throw new Exception('Reference object $ref attribute is mandatory.', $path);
        }
        if (!\is_string($data['$ref'])) {
            throw new Exception('Reference object $ref attribute must be a string.', $path);
        }

        return new self($data['$ref'], $path);
    }

    /**
     * @param list<string> $path
     */
    private function __construct(
        public readonly string $ref,
        public readonly array $path,
    ) {
    }

    public function getName(): string
    {
        // TODO check errors
        return explode('/', $this->ref)[3];
    }
}
