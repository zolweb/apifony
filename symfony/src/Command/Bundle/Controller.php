<?php

namespace App\Command\Bundle;

class Controller implements File
{
    /**
     * @param array<Action> $actions
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        Handler $handler,
    ): self {
        $usedFormatConstraintNames = [];
        foreach ($actions as $action) {
            foreach ($action->getParameters() as $parameter) {
                foreach ($parameter->getConstraints() as $constraint) {
                    foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                        $usedFormatConstraintNames[$constraintName] = true;
                    }
                }
            }
        }

        return new self(
            $handler,
            $actions,
            $bundleNamespace,
            $aggregateName,
            array_keys($usedFormatConstraintNames),
        );
    }

    /**
     * @param array<Action> $actions
     * @param array<string> $usedFormatConstraintNames
     */
    private function __construct(
        public readonly Handler $handler,
        public readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedFormatConstraintNames,
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
    public function getUsedFormatConstraintNames(): array
    {
        return $this->usedFormatConstraintNames;
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Controller";
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

    public function getParametersRootName(): string
    {
        return 'controller';
    }
}