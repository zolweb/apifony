<?php

namespace App\Command\Bundle;

class FormatValidator implements File
{
    public static function build(
        string $bundleNamespace,
        string $formatName,
        FormatDefinition $formatDefinition,
    ): self {
        return new self(
            $bundleNamespace,
            $formatName,
            $formatDefinition,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $formatName,
        private readonly FormatDefinition $formatDefinition,
    ) {
    }

    public function getDefinitionInterfaceName(): string
    {
        return $this->formatDefinition->getInterfaceName();
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Format";
    }

    public function getClassName(): string
    {
        return "{$this->formatName}Validator";
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->formatName}Validator.php";
    }

    public function getTemplate(): string
    {
        return 'format-validator.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'validator';
    }
}