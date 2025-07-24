<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Symfony\Component\Yaml\Yaml;

use function Symfony\Component\String\u;

class RoutesConfig implements File
{
    // /**
    //  * @return list<Operation>
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
     * @param list<Controller> $controllers
     */
    private function __construct(
        private readonly string $namespace,
        private readonly array $controllers,
    ) {
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

        $serviceNamespace = u($this->namespace)->snake()->toString();

        foreach ($this->controllers as $controller) {
            foreach ($controller->actions as $action) {
                $routes["{$serviceNamespace}_{$action->getServiceName()}"] = $action->getRoute("{$controller->getNamespace()}\\{$controller->getClassName()}");
            }
        }

        return Yaml::dump($routes, 100);
    }
}
