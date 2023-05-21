<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Operation;

class Handler implements PhpClassFile
{
    /** @var array<Operation> */
    public readonly array $operations;

    private readonly string $bundleNamespace;
    private readonly string $aggregateName;

    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $operations,
    ): self {
        $aggregate = new self();
        $aggregate->bundleNamespace = $bundleNamespace;
        $aggregate->aggregateName = $aggregateName;
        $aggregate->operations = $operations;

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