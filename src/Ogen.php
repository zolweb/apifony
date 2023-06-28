<?php

namespace Zol\Ogen;

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
        $spec = Yaml::parseFile($openApiSpecPath);
        $openApi = OpenApi::build($spec);
        $bundle = Bundle::build($bundleName, $packageName, $namespace, $openApi);

        $twig = new Environment(new FilesystemLoader(__DIR__.'/../templates'));

        foreach ($bundle->getFiles() as $file) {
            if (!file_exists("{$outputDir}/{$file->getFolder()}")) {
                mkdir("{$outputDir}/{$file->getFolder()}", recursive: true);
            }

            try {
                $content = $twig->render($file->getTemplate(), [$file->getParametersRootName() => $file]);
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                throw new RuntimeException($e->getMessage(), 0, $e);
            }

            file_put_contents("{$outputDir}/{$file->getFolder()}/{$file->getName()}", $content);
        }
    }
}
