<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Zol\Apifony\Tests\TestOpenApiServer\TestOpenApiServerBundle;

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
        $frameworkConfig = [
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'validation' => [
                'email_validation_mode' => 'html5',
            ],
            'php_errors' => [
                'log' => true,
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
            'test' => true,
        ];

        if (class_exists(\Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor::class)) {
            $frameworkConfig['property_info'] = [
                'with_constructor_extractor' => true,
            ];
        }

        $container->extension('framework', $frameworkConfig);

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
