<?php

namespace App\Command;

use App\Command\Bundle\Bundle;
use App\Command\Bundle\Exception as BundleException;
use App\Command\OpenApi\OpenApi;
use App\Command\OpenApi\Exception as OpenApiException;
use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class GenService
{
    /**
     * @param array<mixed> $data
     *
     * @throws OpenApiException
     * @throws BundleException
     */
    public function generate(array $data, string $bundleName, string $namespace, string $packageName): void
    {
        $openApi = OpenApi::build($data);
        $bundle = Bundle::build($bundleName, $packageName, $namespace, $openApi);

        $twig = new Environment(new FilesystemLoader(__DIR__.'/../../templates'));

        foreach ($bundle->getFiles() as $file) {
            if (!file_exists(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}")) {
                mkdir(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}", recursive: true);
            }

            try {
                $content = $twig->render($file->getTemplate(), [$file->getParametersRootName() => $file]);
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                throw new RuntimeException($e->getMessage(), 0, $e);
            }

            file_put_contents(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}/{$file->getName()}", $content);
        }
    }
}
