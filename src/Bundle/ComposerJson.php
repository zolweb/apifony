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
                'require' => [
                    'phpdocumentor/type-resolver' => '^1.8', // Required by PhpStanExtractor
                    'phpstan/phpdoc-parser' => '^1.13', // Required by PhpStanExtractor
                    'symfony/dependency-injection' => '7.1.*',
                    'symfony/http-foundation' => '7.1.*',
                    'symfony/http-kernel' => '7.1.*',
                    'symfony/serializer' => '7.1.*',
                    'symfony/validator' => '7.1.*',
                    'symfony/property-access' => '7.1.*', // Required by ObjectNormalizer
                    'symfony/property-info' => '7.1.*',
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
