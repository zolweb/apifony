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
     * @param array<Action> $actions
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
    ): self {
        $handler = new self();
        $handler->bundleNamespace = $bundleNamespace;
        $handler->aggregateName = $aggregateName;

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