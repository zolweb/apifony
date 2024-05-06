<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use Symfony\Component\Yaml\Yaml;

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
        return u($this->namespace)->snake()->toString();
    }

    public function getFolder(): string
    {
        return 'config';
    }

    public function getName(): string
    {
        return 'routes.yaml';
    }

    public function getContent(): string
    {
        $routes = [];

        foreach ($this->controllers as $controller) {
            foreach ($controller->actions as $action) {
                $routes["{$this->getServiceNamespace()}_{$action->getServiceName()}"] = [
                    'path' => $action->getRoute(),
                    'methods' => $action->getMethod(),
                    'controller' => "{$controller->getNamespace()}\\{$controller->getClassName()}::{$action->getName()}",
                ];
                if (\count($action->getParameters(['path'])) > 0) {
                    $routes["{$this->getServiceNamespace()}_{$action->getServiceName()}"]['requirements'] = [];
                    foreach ($action->getParameters(['path']) as $parameter) {
                        $routes["{$this->getServiceNamespace()}_{$action->getServiceName()}"]['requirements'][$parameter->getRawName()] = $parameter->getRouteRequirementPattern();
                    }
                }
            }
        }

        return Yaml::dump($routes, 100);
    }
}
