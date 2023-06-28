<?php

namespace Zol\Ogen\Bundle;

class DenormalizationException implements File
{
    public function __construct(
        private readonly string $bundleNamespace,
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api";
    }

    public function getFolder(): string
    {
        return 'src/Api';
    }

    public function getName(): string
    {
        return 'DenormalizationException.php';
    }

    public function getTemplate(): string
    {
        return 'denormalization-exception.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'exception';
    }
}