<?php

namespace Zol\Ogen\Tests\TestOpenApiServer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TestOpenApiServerBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new class () implements CompilerPassInterface {
                public function process(ContainerBuilder $container): void {
                    foreach ($container->findTaggedServiceIds('test_open_api_server.handler') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['controller']) {
                                case 'atom':
                                    $container
                                        ->findDefinition('Zol\Ogen\Tests\TestOpenApiServer\Api\Atom\AtomController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;
                            }
                        }
                    }

                    foreach ($container->findTaggedServiceIds('test_open_api_server.format_definition') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['format']) {
                            }
                        }
                    }
                }
            }
        );
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }
}
