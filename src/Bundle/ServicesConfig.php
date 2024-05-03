<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use Symfony\Component\Yaml\Yaml;

use function Symfony\Component\String\u;

class ServicesConfig implements File
{
    /**
     * @param array<string, Format> $formats
     */
    public static function build(
        string $namespace,
        Api $api,
        array $formats,
    ): self {
        $controllers = [];
        foreach ($api->getAggregates() as $aggregate) {
            $controllers[] = $aggregate->getController();
        }

        $formatValidators = [];
        foreach ($formats as $format) {
            $formatValidators[] = $format->getValidator();
        }

        return new self(
            $namespace,
            $controllers,
            $formatValidators,
        );
    }

    /**
     * @param array<Controller>      $controllers
     * @param array<FormatValidator> $formatValidators
     */
    private function __construct(
        private readonly string $namespace,
        private readonly array $controllers,
        private readonly array $formatValidators,
    ) {
    }

    public function getServiceNamespace(): string
    {
        return u($this->namespace)->snake();
    }

    /**
     * @return array<Controller>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @return array<FormatValidator>
     */
    public function getFormatValidators(): array
    {
        return $this->formatValidators;
    }

    public function getFolder(): string
    {
        return 'config';
    }

    public function getName(): string
    {
        return 'services.yaml';
    }

    public function getTemplate(): string
    {
        return 'services.yaml.twig';
    }

    public function getParametersRootName(): string
    {
        return 'services';
    }

    public function getContent(): string
    {
        $config = ['services' => []];

        foreach ($this->controllers as $controller) {
            $config['services']["{$controller->getNamespace()}\\{$controller->getClassName()}"] = [
                'class' => "{$controller->getNamespace()}\\{$controller->getClassName()}",
                'arguments' => [
                    '$serializer' => "@{$this->getServiceNamespace()}.serializer_interface",
                    '$validator' => '@validator',
                ],
                'public' => true,
            ];
        }

        foreach ($this->formatValidators as $formatValidator) {
            $config['services']["{$formatValidator->getNamespace()}\\{$formatValidator->getClassName()}"] = [
                'class' => "{$formatValidator->getNamespace()}\\{$formatValidator->getClassName()}",
                'public' => true,
                'tags' => ['validator.constraint_validator'],
            ];
        }

        $config['services']["{$this->getServiceNamespace()}.json_encoder"] = [
            'class' => 'Symfony\Component\Serializer\Encoder\JsonEncoder',
        ];

        $config['services']["{$this->getServiceNamespace()}.php_stan_extractor"] = [
            'class' => 'Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor',
        ];

        $config['services']["{$this->getServiceNamespace()}.reflection_extractor"] = [
            'class' => 'Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor',
        ];

        $config['services']["{$this->getServiceNamespace()}.property_info_extractor"] = [
            'class' => 'Symfony\Component\PropertyInfo\PropertyInfoExtractor',
            'arguments' => [
                '$typeExtractors' => [
                    "@{$this->getServiceNamespace()}.php_stan_extractor",
                    "@{$this->getServiceNamespace()}.reflection_extractor",
                ],
            ],
        ];

        $config['services']["{$this->getServiceNamespace()}.attribute_loader"] = [
            'class' => 'Symfony\Component\Serializer\Mapping\Loader\AttributeLoader',
        ];

        $config['services']["{$this->getServiceNamespace()}.class_metadata_factory"] = [
            'class' => 'Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory',
            'arguments' => [
                '$loader' => "@{$this->getServiceNamespace()}.attribute_loader",
            ],
        ];

        $config['services']["{$this->getServiceNamespace()}.metadata_aware_name_converter"] = [
            'class' => 'Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter',
            'arguments' => [
                '$metadataFactory' => "@{$this->getServiceNamespace()}.class_metadata_factory",
            ],
        ];

        $config['services']["{$this->getServiceNamespace()}.object_normalizer"] = [
            'class' => 'Symfony\Component\Serializer\Normalizer\ObjectNormalizer',
            'arguments' => [
                '$propertyTypeExtractor' => "@{$this->getServiceNamespace()}.property_info_extractor",
                '$classMetadataFactory' => "@{$this->getServiceNamespace()}.class_metadata_factory",
                '$nameConverter' => "@{$this->getServiceNamespace()}.metadata_aware_name_converter",
            ],
        ];

        $config['services']["{$this->getServiceNamespace()}.array_denormalizer"] = [
            'class' => 'Symfony\Component\Serializer\Normalizer\ArrayDenormalizer',
        ];

        $config['services']["{$this->getServiceNamespace()}.serializer_interface"] = [
            'class' => 'Symfony\Component\Serializer\Serializer',
            'arguments' => [
                '$encoders' => [
                    "@{$this->getServiceNamespace()}.json_encoder",
                ],
                '$normalizers' => [
                    "@{$this->getServiceNamespace()}.object_normalizer",
                    "@{$this->getServiceNamespace()}.array_denormalizer",
                ],
            ],
        ];

        return Yaml::dump($config, 100);
    }
}
