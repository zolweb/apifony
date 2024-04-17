<?php

namespace Zol\Ogen;

// use PhpParser\NodeDumper;
// use PhpParser\ParserFactory;
// use PhpParser\PhpVersion;
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

//     private const CODE = <<<'PHP'
// <?php
//
// namespace Zol\Ogen\Tests\TestOpenApiServer\Api;
//
// class RequestBodyValidationException extends \Exception
// {
//     /**
//      * @param array<string, array<string>> $messages
//      */
//     public function __construct(public readonly array $messages)
//     {
//         parent::__construct();
//     }
// }
// PHP;

}
