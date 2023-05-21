<?php

namespace App\Command\Bundle;

class Controller implements PhpClassFile
{
    public readonly Handler $handler;
    /** @var array<Action> */
    public readonly array $actions;

    private readonly string $bundleNamespace;
    private readonly string $aggregateName;
    private readonly AbstractController $abstractController;

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
        $aggregate = new self();
        $aggregate->bundleNamespace = $bundleNamespace;
        $aggregate->aggregateName = $aggregateName;
        $aggregate->abstractController = $abstractController;
        $aggregate->handler = $handler;
        $aggregate->actions = $actions;

        return $aggregate;
    }

    private function __construct()
    {
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