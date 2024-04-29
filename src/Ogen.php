<?php

namespace Zol\Ogen;

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;
use Zol\Ogen\Bundle\Bundle;
use Zol\Ogen\Bundle\Exception as BundleException;
use Zol\Ogen\OpenApi\Exception as OpenApiException;
use Zol\Ogen\OpenApi\OpenApi;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Ogen
{
    /**
     * @throws OpenApiException
     * @throws BundleException
     */
    public function generate(string $bundleName, string $namespace, string $packageName, string $openApiSpecPath, string $outputDir): void
    {
        // $parser = (new ParserFactory())->createForVersion(PhpVersion::fromString('8.2'));
        // $stmts = $parser->parse(self::CODE);
        // $nodeDumper = new NodeDumper(['dumpComments' => true]);
        // echo $nodeDumper->dump($stmts), "\n";
        // exit;

        $spec = Yaml::parseFile($openApiSpecPath);

        if (!is_array($spec)) {
            throw new RuntimeException('Yaml content must be an array');
        }

        $openApi = OpenApi::build($spec);
        $bundle = Bundle::build($bundleName, $packageName, $namespace, $openApi);

        $twig = new Environment(new FilesystemLoader(__DIR__.'/../templates'));
        $astPrinter = new Standard();

        foreach ($bundle->getFiles() as $file) {
            if (!file_exists("{$outputDir}/{$file->getFolder()}")) {
                mkdir("{$outputDir}/{$file->getFolder()}", recursive: true);
            }

            if ($file->hasNamespaceAst()) {
                $content = $astPrinter->prettyPrintFile([$file->getNamespaceAst()]);
            } else {
                try {
                    $content = $twig->render($file->getTemplate(), [$file->getParametersRootName() => $file]);
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                    throw new RuntimeException($e->getMessage(), 0, $e);
                }
            }

            file_put_contents("{$outputDir}/{$file->getFolder()}/{$file->getName()}", $content);
        }
    }

    private const CODE = <<<'PHP'
<?php

namespace Zol\Cortizol\AdminOpenApiServer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class CortizolAdminOpenApiServerBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new class () implements CompilerPassInterface {
                public function process(ContainerBuilder $container): void {
                    foreach ($container->findTaggedServiceIds('cortizol_admin_open_api_server.handler') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['controller']) {
                                case 'content_type':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\ContentType\ContentTypeController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'locale':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\Locale\LocaleController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'content':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\Content\ContentController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'content_translation':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\ContentTranslation\ContentTranslationController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'media':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\Media\MediaController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;

                                case 'media_tree':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Api\MediaTree\MediaTreeController')
                                        ->addMethodCall('setHandler', [new Reference($id)]);

                                break;
                            }
                        }
                    }

                    foreach ($container->findTaggedServiceIds('cortizol_admin_open_api_server.format_definition') as $id => $tags) {
                        foreach ($tags as $tag) {
                            switch ($tag['format']) {
                                case 'media-folder-id':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Format\MediaFolderIdValidator')
                                        ->addMethodCall('setFormatDefinition', [new Reference($id)]);

                                break;

                                case 'uuid':
                                    $container
                                        ->findDefinition('Zol\Cortizol\AdminOpenApiServer\Format\UuidValidator')
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
PHP;

}
