<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use function Symfony\Component\String\u;

#[AsCommand('gen')]
class GenCommand extends Command
{
    public function __construct(
        private readonly Environment $twig,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spec = Yaml::parse(file_get_contents(__DIR__.'/openapi.yaml'));

        var_dump($spec);

        foreach ($spec['paths'] as $route => $path) {
            foreach (array_intersect_key($path, array_fill_keys(['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true)) as $method => $operation) {
                $template = $this->twig->render('controller.php.twig', ['spec' => $spec, 'operation' => $operation, 'route' => $route, 'method' => $method]);
                file_put_contents(__DIR__.'/../Controller/'.u($operation['operationId'])->camel()->title().'Controller.php', $template);
                $template = $this->twig->render('handler.php.twig', ['spec' => $spec, 'operation' => $operation, 'route' => $route, 'method' => $method]);
                file_put_contents(__DIR__.'/../Controller/'.u($operation['operationId'])->camel()->title().'Handler.php', $template);
            }
        }

        return 0;
    }
}