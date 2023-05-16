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
    public function generate(array $data): void
    {
        $this->twig->getExtension(EscaperExtension::class)->setEscaper(
            'phpSingleQuotedString',
            function (Environment $twig, string $string) {
                return str_replace(['\'', '\\'], ['\\\'', '\\\\'], $string);
            }
        );

        foreach (OpenApi::build($data)->getFiles() as $fileName => $file) {
            file_put_contents(
                __DIR__."/../Controller/{$fileName}.php",
                $this->twig->render($file['template'], $file['params']));
        }
    }
}
