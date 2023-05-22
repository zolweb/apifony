<?php

namespace App\Command\Bundle;

class AbstractController implements PhpClassFile
{
    private readonly string $bundleNamespace;

    public static function build(string $bundleNamespace): self
    {
        $abstractController = new self();
        $abstractController->bundleNamespace = $bundleNamespace;

        return $abstractController;
    }

    private function __construct()
    {
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

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api";
    }

    public function getClassName(): string
    {
        return 'AbstractController';
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}