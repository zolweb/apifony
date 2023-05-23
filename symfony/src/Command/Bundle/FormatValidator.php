<?php

namespace App\Command\Bundle;

use function Symfony\Component\String\u;

class FormatValidator implements PhpClassFile
{
    public static function build(
        string $bundleNamespace,
        string $format,
        FormatDefinition $formatDefinition,
    ): self {
        return new self(
            $bundleNamespace,
            u($format)->camel()->title(),
            $formatDefinition,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $format,
        private readonly FormatDefinition $formatDefinition,
    ) {
    }

    public function getDefinitionClassName(): string
    {
        return $this->formatDefinition->getClassName();
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->format}Validator.php";
    }

    public function getTemplate(): string
    {
        return 'format-validator.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'validator';
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Format";
    }

    public function getClassName(): string
    {
        return "{$this->format}Validator";
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}