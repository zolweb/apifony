<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;
use function Symfony\Component\String\u;

class RoutesConfig implements File
{

    // /**
    //  * @return array<Operation>
    //  */
    // public function getAllSortedOperations(): array
    // {
    //     $operations = array_merge(
    //         ...array_map(
    //         static fn (PathItem $pathItem) => $pathItem->operations,
    //         $this->pathItems,
    //     ),
    //     );

    //     usort(
    //         $operations,
    //         static fn (Operation $operation1, Operation $operation2) =>
    //         $operation2->priority - $operation1->priority ?:
    //             strcmp($operation1->operationId, $operation2->operationId),
    //     );

    //     return $operations;
    // }
    public static function build(
        string $namespace,
        Api $api,
    ): self {
        $controllers = [];

        foreach ($api->getAggregates() as $aggregate) {
            $controllers[] = $aggregate->getController();
        }

        return new self(
            $namespace,
            $controllers,
        );
    }

    /**
     * @param array<Controller> $controllers
     */
    private function __construct(
        private readonly string $namespace,
        private readonly array $controllers,
    ) {
    }

    public function getServiceNamespace(): string
    {
        return u($this->namespace)->snake();
    }

    /**
     * @return array<Controller>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    public function getFolder(): string
    {
        return 'config';
    }

    public function getName(): string
    {
        return 'routes.yaml';
    }

    public function getTemplate(): string
    {
        return 'routes.yaml.twig';
    }

    public function getParametersRootName(): string
    {
        return 'routes';
    }

    public function hasNamespaceAst(): bool
    {
        return false;
    }

    public function getNamespaceAst(): Namespace_
    {
        throw new \RuntimeException();
    }
}