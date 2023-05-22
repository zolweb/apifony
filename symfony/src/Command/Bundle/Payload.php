<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Schema;

class Payload implements PhpClassFile
{
    public static function build(
        string $bundleNamespace,
        string $name,
        Schema $schema,
    ): self {
        return new self(
            $bundleNamespace,
            $name,
            $schema,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $name,
        public readonly Schema $schema,
    ) {
    }

    public function getFolder(): string
    {
        return 'src/Payload';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): string
    {
        return 'payload.php.twig';
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Payload";
    }

    public function getClassName(): string
    {
        return $this->name;
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}