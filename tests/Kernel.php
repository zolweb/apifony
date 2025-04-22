<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Zol\Ogen\Tests\TestOpenApiServer\TestOpenApiServerBundle;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TestOpenApiServerBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/bundle/config/routes.yaml');
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => [
                'log' => true,
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
            'test' => true,
        ]);

        $container->services()
            ->set(TestHandler::class)
            ->tag('test_open_api_server.handler', ['controller' => 'tag'])
        ;

        $container->services()
            ->set(CustomDefinition::class)
            ->tag('test_open_api_server.format_definition', ['format' => 'custom'])
        ;
    }
}
