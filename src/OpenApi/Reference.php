<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Reference
{
    /**
     * Apifony resolves a reference by looking its name up in the matching components bucket, so
     * the only shape it can act on is a pointer into that object. A pointer into another document,
     * or one that goes on past the entry itself, names nothing it could find.
     */
    private const PATTERN = '#^\#/components/[A-Za-z0-9_-]+/([^/]+)$#';

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
        if (preg_match(self::PATTERN, $data['$ref'], $matches) !== 1) {
            throw new Exception('Reference object $ref attribute must point at a components entry, as in \'#/components/schemas/Name\'.', $path);
        }

        return new self($data['$ref'], $matches[1], $path);
    }

    /**
     * @param list<string> $path
     */
    private function __construct(
        public readonly string $ref,
        private readonly string $name,
        public readonly array $path,
    ) {
    }

    /**
     * The components entry this reference names.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
