<?php

namespace App\Command\Bundle;

use function Symfony\Component\String\u;

class FormatConstraint implements PhpClassFile
{
    public static function build(
        string $bundleNamespace,
        string $format,
    ): self {
        return new self(
            $bundleNamespace,
            u($format)->camel()->title(),
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $format,
    ) {
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->format}.php";
    }

    public function getTemplate(): string
    {
        return 'format-constraint.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'constraint';
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Format";
    }

    public function getClassName(): string
    {
        return $this->format;
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}