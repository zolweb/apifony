<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand('gen')]
class GenCommand extends Command
{
    public function __construct(
        private readonly GenService $genService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spec = Yaml::parse(file_get_contents(__DIR__.'/../../openapi/invoicing/openapi.yaml'));

        $this->genService->generate(
            $spec,
            'InvoicingOpenApiServer',
            'App\\Zol\\Invoicing\\Presentation\\Api\\Bundle',
            'zol/invoicing-api',
        );

        return 0;
    }
}
