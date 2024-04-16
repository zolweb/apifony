<?php

namespace Zol\TestOpenApiServer;

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
                                case 'content_type':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\ContentType\ContentTypeController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'locale':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\Locale\LocaleController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'content':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\Content\ContentController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'content_translation':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\ContentTranslation\ContentTranslationController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'media':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\Media\MediaController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'media_tree':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Api\MediaTree\MediaTreeController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;
                            }
                        }
                    }

                    foreach ($container->findTaggedServiceIds('test_open_api_server.format_definition') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['format']) {
                                case 'media-folder-id':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Format\MediaFolderIdValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'uuid':
                                    $container
                                        ->findDefinition('Zol\TestOpenApiServer\Format\UuidValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

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
