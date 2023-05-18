<?php

namespace App\Command;

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
        $this->twig->addGlobal('namespace', $namespace);
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

        $files = $openApi->getFiles();
        $files[] = [
            'folder' => '',
            'name' => 'composer.json',
            'template' => 'composer.json.twig',
            'params' => [
                'bundleName' => $bundleName,
                'packageName' => $packageName,
            ],
        ];
        $files[] = [
            'folder' => 'src',
            'name' => "{$bundleName}.php",
            'template' => 'bundle.php.twig',
            'params' => [
                'bundleName' => $bundleName,
            ],
        ];
        $files[] = [
            'folder' => 'config',
            'name' => 'routing.yaml',
            'template' => 'routing.yaml.twig',
            'params' => [
                'paths' => $openApi->paths,
            ],
        ];

        foreach ($files as $file) {
            if (!file_exists(__DIR__."/../../openapi/invoicing/bundle/{$file['folder']}")) {
                mkdir(__DIR__."/../../openapi/invoicing/bundle/{$file['folder']}", recursive: true);
            }

            file_put_contents(
                __DIR__."/../../openapi/invoicing/bundle/{$file['folder']}/{$file['name']}",
                $this->twig->render($file['template'], $file['params']));
        }
    }
}
