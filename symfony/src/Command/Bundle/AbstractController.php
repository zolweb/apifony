<?php

namespace App\Command\Bundle;

class AbstractController implements File
{
    public static function build(string $bundleNamespace): self
    {
        return new self($bundleNamespace);
    }

    private function __construct(
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
        return 'AbstractController.php';
    }

    public function getTemplate(): string
    {
        return 'abstract-controller.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'abstractController';
    }
}