<?php

namespace Zol\Ogen\Bundle;

use PhpParser\Node\Stmt\Namespace_;

class ComposerJson implements File
{
    public function __construct(
        private readonly string $packageName,
        private readonly string $namespace,
    ) {
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFolder(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'composer.json';
    }

    public function getTemplate(): string
    {
        return 'composer.json.twig';
    }

    public function getParametersRootName(): string
    {
        return 'composer';
    }

    public function getContent(): string
    {
        try {
            return json_encode([
                'name' => $this->packageName,
                'require' => [
                    'doctrine/annotations' => '^2.0',
                    'phpdocumentor/reflection-docblock' => '^5.3',
                    'phpstan/phpdoc-parser' => '^1.24',
                    'symfony/dependency-injection' => '7.0.*',
                    'symfony/http-foundation' => '7.0.*',
                    'symfony/http-kernel' => '7.0.*',
                    'symfony/serializer' => '7.0.*',
                    'symfony/validator' => '7.0.*',
                    'symfony/property-access' => '7.0.*',
                    'symfony/property-info' => '7.0.*'
                ],
                'autoload' => [
                    'psr-4' => [
                        "{$this->namespace}\\" => 'src/'
                    ],
                ],
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        } catch (\JsonException) {
            throw new \RuntimeException();
        }
    }
}