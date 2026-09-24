<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Symfony\Component\Yaml\Yaml;

class RoutesConfig implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $namespace,
        Api $api,
        NameRegistry $names,
    ): self {
        $serviceNamespace = Naming::forServiceId($namespace);

        $routes = [];
        foreach ($api->getAggregates() as $aggregate) {
            $controller = $aggregate->getController();
            $action = $controller->action;
            $name = "{$serviceNamespace}_{$action->getServiceName()}";
            $names->claimRoute($name, Origin::spec('operation', $aggregate->getName(), ['documentation root']));
            $routes[$name] = $action->getRoute("{$controller->getNamespace()}\\{$controller->getClassName()}");
        }

        return new self($routes);
    }

    /**
     * @param array<string, array{path: string, methods: string, controller: string, requirements?: array<string, string>}> $routes
     */
    private function __construct(
        private readonly array $routes,
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
        return Yaml::dump($this->routes, 100);
    }
}
