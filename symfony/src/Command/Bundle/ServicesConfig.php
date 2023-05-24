<?php

namespace App\Command\Bundle;

use function Symfony\Component\String\u;

class ServicesConfig implements File
{
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
        return 'services.yaml';
    }

    public function getTemplate(): string
    {
        return 'services.yaml.twig';
    }

    public function getParametersRootName(): string
    {
        return 'services';
    }
}