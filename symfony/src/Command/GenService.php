<?php

namespace App\Command;

use App\Command\Bundle\Bundle;
use App\Command\OpenApi\Exception;
use App\Command\OpenApi\OpenApi;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GenService
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     * @throws Bundle\Exception
     */
    public function generate(array $data, string $bundleName, string $namespace, string $packageName): void
    {
        $openApi = OpenApi::build($data);
        $bundle = Bundle::build($bundleName, $packageName, $namespace, $openApi);

        foreach ($bundle->getFiles() as $file) {
            if (!file_exists(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}")) {
                mkdir(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}", recursive: true);
            }

            file_put_contents(
                __DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}/{$file->getName()}",
                $this->twig->render($file->getTemplate(), [$file->getParametersRootName() => $file]));
        }
    }
}
