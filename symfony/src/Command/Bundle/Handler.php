<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;

class Handler implements PhpClassFile
{
    /** @var array<Action> */
    public readonly array $actions;

    private readonly string $bundleNamespace;
    private readonly string $aggregateName;

    /**
     * @param array<Operation> $operations
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $operations,
        Components $components,
    ): self {
        $handler = new self();
        $handler->bundleNamespace = $bundleNamespace;
        $handler->aggregateName = $aggregateName;
        $handler->actions = array_map(
            static fn (Operation $operation) => Action::build($operation, $components),
            $operations,
        );

        return $handler;
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