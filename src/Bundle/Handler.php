<?php

namespace Zol\Ogen\Bundle;

class Handler implements File
{
    /**
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        array $usedModelNames,
    ): self {
        return new self(
            $actions,
            $bundleNamespace,
            $aggregateName,
            $usedModelNames,
        );
    }

    /**
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
     */
    private function __construct(
        private readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedModelNames,
    ) {
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * @return array<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->actions as $action) {
            foreach ($action->getFiles() as $file) {
                $files[] = $file;
            }
        }

        return $files;
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