<?php

namespace App\Command\Bundle;

class Controller implements PhpClassFile
{
    /**
     * @param array<Action> $actions
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        AbstractController $abstractController,
        Handler $handler,
    ): self {
        return new self(
            $handler,
            $actions,
            $bundleNamespace,
            $aggregateName,
            $abstractController,
        );
    }

    private function __construct(
        public readonly Handler $handler,
        /** @var array<Action> */
        public readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly AbstractController $abstractController,
    ) {
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->aggregateName}Controller.php";
    }

    public function getTemplate(): string
    {
        return 'controller.php.twig';
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Controller";
    }

    public function getUsedPhpClassFiles(): array
    {
        return [
            $this->abstractController,
        ];
    }
}