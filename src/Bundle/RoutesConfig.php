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
            $routes["{$serviceNamespace}_{$controller->action->getServiceName()}"] = $controller->action->getRoute("{$controller->getNamespace()}\\{$controller->getClassName()}");
        }

        return Yaml::dump($routes, 100);
    }
}
