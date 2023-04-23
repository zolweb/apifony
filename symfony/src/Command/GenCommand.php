<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

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
        $spec = Yaml::parse(file_get_contents('https://stoplight.io/api/v1/projects/bfav-zol/zol-skeleton/nodes/zol-invoicing.yaml'));

        var_dump($spec);

        foreach ($spec['paths'] as $path => $pathSpec) {
            $pathParams = [];
            if (isset($pathSpec['parameters'])) {
                $paramsSpec = $pathSpec['parameters'];
                foreach ($paramsSpec as $paramSpec) {
                    if ($paramSpec['in'] === 'path') {
                        $pathParam = [
                            'name' => $paramSpec['name'],
                            'type' => $paramSpec['schema']['type'],
                            'constraints' => [],
                        ];
                        if (isset($paramSpec['schema']['format'])) {
                            $pathParam['constraints'][] = $this->createFormatConstraint($paramSpec['schema']['format']);
                        }
                        $pathParams[] = $pathParam;
                    }
                }
            }
            if (isset($pathSpec['post'])) {
                $postSpec = $pathSpec['post'];
                $fileName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])).'Controller.php';
                $templateParams = [
                    'controllerName' => ucfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])).'Controller',
                    'handlerName' => ucfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])).'Handler',
                    'path' => $path,
                    'pathParams' => $pathParams,
                    'actionName' => lcfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])),
                ];
                file_put_contents(__DIR__."/../Controller/$fileName", $this->twig->render('controller.php.twig', $templateParams));

                $fileName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])).'Handler.php';
                $templateParams = [
                    'handlerName' => ucfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId']).'Handler'),
                    'pathParams' => $pathParams,
                    'methodName' => lcfirst(preg_replace('/[^a-z0-9]/i', '', $postSpec['operationId'])),
                ];
                file_put_contents(__DIR__."/../Controller/$fileName", $this->twig->render('handler.php.twig', $templateParams));
            }
        }

        return Command::SUCCESS;
    }

    private function createFormatConstraint(string $format): string
    {
        static $formats = [];

        $constraintName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $format));

        if (!isset($formats[$format])) {
            $fileName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $format)).'.php';
            $templateParams = [
                'constraintName' => $constraintName,
            ];
            file_put_contents(__DIR__."/../Controller/$fileName", $this->twig->render('constraint.php.twig', $templateParams));

            $fileName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $format)).'Validator.php';
            $templateParams = [
                'validatorName' => $constraintName.'Validator',
                'validatorInterfaceName' => $constraintName.'ValidatorInterface',
            ];
            file_put_contents(__DIR__."/../Controller/$fileName", $this->twig->render('validator.php.twig', $templateParams));

            $fileName = ucfirst(preg_replace('/[^a-z0-9]/i', '', $format)).'ValidatorInterface.php';
            $templateParams = [
                'validatorInterfaceName' => $constraintName.'ValidatorInterface',
            ];
            file_put_contents(__DIR__."/../Controller/$fileName", $this->twig->render('validator-interface.php.twig', $templateParams));

            $formats[] = $format;
        }

        return $constraintName;
    }
}