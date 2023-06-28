<?php

namespace AppZolInvoicingPresentationApiBundle;

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
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Api\Default\DefaultController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'pet':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Api\Pet\PetController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'store':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Api\Store\StoreController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'user':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Api\User\UserController')
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
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\Int64Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'int32':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\Int32Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'date-time':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\DateTimeValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f21':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F21Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f22':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F22Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f23':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F23Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f25':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F25Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'format':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\FormatValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f1':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F1Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f2':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F2Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f3':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F3Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f4':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F4Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f5':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F5Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f6':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F6Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f7':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F7Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f9':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F9Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f10':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F10Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f13':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F13Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f12':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F12Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f14':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F14Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f15':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F15Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f16':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F16Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f17':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F17Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f18':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F18Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f19':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F19Validator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'f20':
                                    $container
                                        ->findDefinition('AppZolInvoicingPresentationApiBundle\Format\F20Validator')
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
