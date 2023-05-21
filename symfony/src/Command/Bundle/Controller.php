<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;

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
        array $operations,
        Components $components,
        AbstractController $abstractController,
        Handler $handler,
    ): self {
        $controller = new self();
        $controller->bundleNamespace = $bundleNamespace;
        $controller->aggregateName = $aggregateName;
        $controller->abstractController = $abstractController;
        $controller->handler = $handler;
        $controller->actions = array_map(
            static fn (Operation $operation) => Action::build($operation, $components),
            $operations,
        );

        return $controller;
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