<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class InvoicingOpenApiServerBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new class () implements CompilerPassInterface {
                public function process(ContainerBuilder $container): void {
                    foreach ($container->findTaggedServiceIds('invoicing_open_api_server.handler') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['controller']) {
                                case 'default':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Default\DefaultController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'pet':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Pet\PetController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'store':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Store\StoreController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'user':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Controller\User\UserController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;
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
