<?php

namespace App\Command\Bundle;

class Handler implements File
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

    /**
     * @param array<Action> $actions
     */
    private function __construct(
        private readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
    ) {
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Handler";
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

    public function getParametersRootName(): string
    {
        return 'handler';
    }
}