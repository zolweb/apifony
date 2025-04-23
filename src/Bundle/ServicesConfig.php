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
     * @param list<Controller>                                                                                  $controllers
     * @param array<FormatValidator|EmailValidator|UuidValidator|DateTimeValidator|DateValidator|TimeValidator> $formatValidators
     */
    private function __construct(
        private readonly string $namespace,
        private readonly array $controllers,
        private readonly array $formatValidators,
    ) {
    }

    public function getServiceNamespace(): string
    {
        return u($this->namespace)->snake()->toString();
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
        $config = ['services' => []];

        foreach ($this->controllers as $controller) {
            $config['services']["{$controller->getNamespace()}\\{$controller->getClassName()}"] = [
                'class' => "{$controller->getNamespace()}\\{$controller->getClassName()}",
                'arguments' => [
                    '$deserializer' => "@{$this->getServiceNamespace()}.deserializer_interface",
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

        $config['services']["{$this->getServiceNamespace()}.deserializer_interface"] = [
            'class' => "{$this->namespace}\\Api\\Deserializer",
        ];

        return Yaml::dump($config, 100);
    }
}
