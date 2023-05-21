<?php

namespace App\Command;

use App\Command\Bundle\Bundle;
use App\Command\OpenApi\Exception;
use App\Command\OpenApi\OpenApi;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;

class GenService extends AbstractExtension
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
     */
    public function generate(array $data, string $bundleName, string $namespace, string $packageName): void
    {
        $this->twig->getExtension(EscaperExtension::class)->setEscaper(
            'phpSingleQuotedString',
            function (Environment $twig, string $string) {
                return addcslashes($string, '\'\\');
            }
        );
        $this->twig->getExtension(EscaperExtension::class)->setEscaper(
            'jsonString',
            function (Environment $twig, string $string) {
                return addcslashes($string, '"\\');
            }
        );

        $openApi = OpenApi::build($data);
        $bundle = Bundle::build($namespace, $openApi);
        $files = $bundle->getFiles();

        // foreach ($bundle->aggregates as $aggregate) {
        //     $files[] = [
        //         'folder' => 'src/Controller',
        //         'name' => 'AbstractController.php',
        //         'template' => 'abstract-controller.php.twig',
        //         'params' => [],
        //     ];
        //     foreach ($aggregate->responsex as $responsex) {
        //         $files[] = [
        //             'folder' => 'src/Controller',
        //             'name' => 'AbstractController.php',
        //             'template' => 'abstract-controller.php.twig',
        //             'params' => [],
        //         ];
        //     }
        // }

        // // $files = $openApi->getFiles();
        // $files[] = [
        //     'folder' => 'src/Controller',
        //     'name' => 'AbstractController.php',
        //     'template' => 'abstract-controller.php.twig',
        //     'params' => [],
        // ];
        // $files[] = [
        //     'folder' => '',
        //     'name' => 'composer.json',
        //     'template' => 'composer.json.twig',
        //     'params' => [
        //         'packageName' => $packageName,
        //     ],
        // ];
        // $files[] = [
        //     'folder' => 'src',
        //     'name' => "{$bundleName}Bundle.php",
        //     'template' => 'bundle.php.twig',
        //     'params' => [
        //         'bundleName' => $bundleName,
        //         'paths' => $openApi->paths,
        //     ],
        // ];
        // $files[] = [
        //     'folder' => 'config',
        //     'name' => 'services.yaml',
        //     'template' => 'services.yaml.twig',
        //     'params' => [
        //         'paths' => $openApi->paths,
        //     ],
        // ];
        // $files[] = [
        //     'folder' => 'config',
        //     'name' => 'routes.yaml',
        //     'template' => 'routes.yaml.twig',
        //     'params' => [
        //         'paths' => $openApi->paths,
        //     ],
        // ];

        foreach ($files as $file) {
            if (!file_exists(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}")) {
                mkdir(__DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}", recursive: true);
            }

            file_put_contents(
                __DIR__."/../../openapi/invoicing/bundle/{$file->getFolder()}/{$file->getName()}",
                $this->twig->render($file->getTemplate(), ['file' => $file]));
        }
    }
}
