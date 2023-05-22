<?php

namespace App\Command\Bundle;

class Handler implements PhpClassFile
{
    /**
     * @param array<Action> $actions
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
    ): self {
        return new self(
            $actions,
            $bundleNamespace,
            $aggregateName,
        );
    }

    private function __construct(
        /** @var array<Action> */
        public readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
    ) {
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->aggregateName}Handler.php";
    }

    public function getTemplate(): string
    {
        return 'handler.php.twig';
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Handler";
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}