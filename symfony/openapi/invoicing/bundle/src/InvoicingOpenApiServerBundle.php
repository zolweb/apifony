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
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default\DefaultController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'pet':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet\PetController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'store':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store\StoreController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'user':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Api\User\UserController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;
                            }
                        }
                    }

                    foreach ($container->findTaggedServiceIds('invoicing_open_api_server.format_definition') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['format']) {
                                case 'int64':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'int32':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int32Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'date-time':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\DateTimeValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f21':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F21Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f22':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F22Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f23':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F23Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f25':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F25Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'format':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\FormatValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'binary':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\BinaryValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f1':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F1Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f2':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F2Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f3':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F3Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f4':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F4Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f5':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F5Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f6':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F6Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f7':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F7Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f9':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F9Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f10':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F10Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f13':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F13Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f12':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F12Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f14':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F14Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f15':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F15Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f16':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F16Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f17':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F17Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f18':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F18Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f19':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F19Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f20':
                                    $container
                                        ->findDefinition('App\Zol\Invoicing\Presentation\Api\Bundle\Format\F20Validator')
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
