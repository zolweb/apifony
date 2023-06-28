<?php

namespace Zol\Ogen\Bundle;

class RequestBodyValidationException implements File
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
        return 'RequestBodyValidationException.php';
    }

    public function getTemplate(): string
    {
        return 'request-body-validation-exception.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'exception';
    }
}