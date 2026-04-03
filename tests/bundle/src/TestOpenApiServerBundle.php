<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationHandler;
use Zol\Apifony\Tests\TestOpenApiServer\Format\CustomDefinition;
class TestOpenApiServerBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerForAutoconfiguration(FirstOperationHandler::class)->addTag('test_open_api_server.handler', ['controller' => 'first_operation']);
        $container->registerForAutoconfiguration(CustomDefinition::class)->addTag('test_open_api_server.format_definition', ['format' => 'custom']);
        $container->addCompilerPass(new class implements CompilerPassInterface
        {
            public function process(ContainerBuilder $container): void
            {
                foreach ($container->findTaggedServiceIds('test_open_api_server.handler') as $id => $tags) {
                    foreach ($tags as $tag) {
                        switch ($tag['controller']) {
                            case 'first_operation':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation\FirstOperationController')->addMethodCall('setHandler', [new Reference($id)]);
                                break;
                        }
                    }
                }
                foreach ($container->findTaggedServiceIds('test_open_api_server.format_definition') as $id => $tags) {
                    foreach ($tags as $tag) {
                        switch ($tag['format']) {
                            case 'email':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\EmailValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                            case 'uuid':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\UuidValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                            case 'date-time':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\DateTimeValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                            case 'date':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\DateValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                            case 'time':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\TimeValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                            case 'custom':
                                $container->findDefinition('Zol\Apifony\Tests\TestOpenApiServer\Format\CustomValidator')->addMethodCall('setFormatDefinition', [new Reference($id)]);
                                break;
                        }
                    }
                }
            }
        });
    }
    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }
}