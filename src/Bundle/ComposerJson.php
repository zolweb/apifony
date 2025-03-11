<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

class ComposerJson implements File
{
    public function __construct(
        private readonly string $packageName,
        private readonly string $namespace,
    ) {
    }

    public function getFolder(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'composer.json';
    }

    public function getContent(): string
    {
        try {
            return json_encode([
                'name' => $this->packageName,
                'version' => '1.0.0',
                'require' => [
                    'egulias/email-validator' => '^2.1.10|^3|^4', // Required by Symfony Validator
                    'phpdocumentor/type-resolver' => '^1.8', // Required by PhpStanExtractor
                    'phpstan/phpdoc-parser' => '^1.13', // Required by PhpStanExtractor
                    'symfony/dependency-injection' => '^6.3 || ^7.0',
                    'symfony/http-foundation' => '^6.3 || ^7.0',
                    'symfony/http-kernel' => '^6.3 || ^7.0',
                    'symfony/serializer' => '^6.3 || ^7.0',
                    'symfony/validator' => '^6.3 || ^7.0',
                    'symfony/property-access' => '^6.3 || ^7.0', // Required by ObjectNormalizer
                    'symfony/property-info' => '^6.3 || ^7.0',
                ],
                'autoload' => [
                    'psr-4' => [
                        "{$this->namespace}\\" => 'src/',
                    ],
                ],
            ], \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES);
        } catch (\JsonException) {
            throw new \RuntimeException();
        }
    }
}
