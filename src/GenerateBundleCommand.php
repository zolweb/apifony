<?php

declare(strict_types=1);

namespace Zol\Ogen;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Zol\Ogen\Bundle\Bundle;
use Zol\Ogen\Bundle\Exception as BundleException;
use Zol\Ogen\OpenApi\Exception as OpenApiException;
use Zol\Ogen\OpenApi\OpenApi;

#[AsCommand(
    name: 'generate-bundle',
    description: 'Build a server stub as a Symfony bundle from an OpenApi 3.1.0 specification.'
)]
class GenerateBundleCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('bundle-name', InputArgument::REQUIRED, 'The name the generated bundle will have.');
        $this->addArgument('namespace', InputArgument::REQUIRED, 'The namespace in which all classes of the generated bundle will live.');
        $this->addArgument('package-name', InputArgument::REQUIRED, 'The name of the composer package that will host the generated bundle.');
        $this->addArgument('openapi-spec-path', InputArgument::REQUIRED, 'The path to the OpenApi 3.1.0 specification for which the bundle will be generated.');
        $this->addArgument('output-dir', InputArgument::REQUIRED, 'The path of a folder where the bundle will be generated.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        try {
            $bundleName = $input->getArgument('bundle-name');
            $namespace = $input->getArgument('namespace');
            $packageName = $input->getArgument('package-name');
            $openApiSpecPath = $input->getArgument('openapi-spec-path');
            $outputDir = $input->getArgument('output-dir');

            if (!\is_string($bundleName) || !\is_string($namespace) || !\is_string($packageName) || !\is_string($openApiSpecPath) || !\is_string($outputDir)) {
                throw new \RuntimeException('Unexpected non string required parameter.');
            }

            $spec = Yaml::parseFile($openApiSpecPath);

            if (!\is_array($spec)) {
                throw new \RuntimeException('Yaml content must be an array');
            }

            $openApi = OpenApi::build($spec);
            $bundle = Bundle::build(
                $bundleName,
                $packageName,
                $namespace,
                $openApi,
            );

            foreach ($bundle->getFiles() as $file) {
                if (!file_exists("{$outputDir}/{$file->getFolder()}")) {
                    mkdir("{$outputDir}/{$file->getFolder()}", recursive: true);
                }

                file_put_contents("{$outputDir}/{$file->getFolder()}/{$file->getName()}", $file->getContent());
            }

            $style->success('Bundle generated successfully! 😂');

            return Command::SUCCESS;
        } catch (BundleException|OpenApiException $e) {
            $style->error([
                "{$e->getMessage()} 🤪",
                \sprintf("Error location:\n  -> %s", implode("\n  -> ", $e->path)),
            ]);

            return Command::FAILURE;
        }
    }
}
