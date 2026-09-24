<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Symfony\Component\Yaml\Yaml;

class ServicesConfig implements File
{
    /**
     * @param array<string, Format> $formats
     *
     * @throws Exception
     */
    public static function build(
        string $namespace,
        Api $api,
        array $formats,
        NameRegistry $names,
    ): self {
        $controllers = [];
        foreach ($api->getAggregates() as $aggregate) {
            $controllers[] = $aggregate->getController();
        }

        $formatValidators = [];
        foreach ($formats as $format) {
            $validator = $format->getValidator();
            if ($validator instanceof FormatValidator) {
                $formatValidators[] = $validator;
            }
        }

        $config = self::buildConfig($namespace, $controllers, $formatValidators);
        foreach (array_keys($config['services']) as $id) {
            $names->claimServiceId($id, Origin::spec('service', $id, ['documentation root']));
        }

        return new self($config);
    }

    /**
     * @param array{services: array<string, mixed>} $config
     */
    private function __construct(
        private readonly array $config,
    ) {
    }

    public function getFolder(): string
    {
        return 'config';
    }

    public function getName(): string
    {
        return 'services.yaml';
    }

    public function getContent(): string
    {
        return Yaml::dump($this->config, 100);
    }

    /**
     * @param list<Controller>       $controllers
     * @param array<FormatValidator> $formatValidators
     *
     * @return array{services: array<string, mixed>}
     */
    private static function buildConfig(string $namespace, array $controllers, array $formatValidators): array
    {
        $serviceNamespace = Naming::forServiceId($namespace);

        $config = ['services' => []];

        foreach ($controllers as $controller) {
            $config['services']["{$controller->getNamespace()}\\{$controller->getClassName()}"] = [
                'class' => "{$controller->getNamespace()}\\{$controller->getClassName()}",
                'arguments' => [
                    '$validator' => "@{$serviceNamespace}.validator",
                ],
                'public' => true,
            ];
        }

        foreach ($formatValidators as $formatValidator) {
            $config['services']["{$formatValidator->getNamespace()}\\{$formatValidator->getClassName()}"] = [
                'class' => "{$formatValidator->getNamespace()}\\{$formatValidator->getClassName()}",
                'public' => true,
                'tags' => ['validator.constraint_validator'],
            ];
        }

        $config['services']["{$serviceNamespace}.constraint_validator_factory"] = [
            'class' => "{$namespace}\\Api\\ConstraintValidatorFactory",
            'calls' => array_map(
                static fn (FormatValidator $formatValidator) => [
                    'addValidator',
                    ["@{$formatValidator->getNamespace()}\\{$formatValidator->getClassName()}"],
                ],
                $formatValidators,
            ),
        ];

        $config['services']["{$serviceNamespace}.validator_builder"] = [
            'class' => 'Symfony\Component\Validator\ValidatorBuilder',
            'factory' => ['Symfony\Component\Validator\Validation', 'createValidatorBuilder'],
            'calls' => [
                ['enableAttributeMapping'],
                ['setConstraintValidatorFactory', ["@{$serviceNamespace}.constraint_validator_factory"]],
            ],
        ];

        $config['services']["{$serviceNamespace}.validator"] = [
            'class' => 'Symfony\Component\Validator\Validation',
            'factory' => ["@{$serviceNamespace}.validator_builder", 'getValidator'],
        ];

        return $config;
    }
}
