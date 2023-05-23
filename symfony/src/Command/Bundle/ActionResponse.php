<?php

namespace App\Command\Bundle;

use function Symfony\Component\String\u;

class ActionResponse implements File
{
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        string $responseCode,
        string $contentType,
    ): self {
        return new self(
            $bundleNamespace,
            $aggregateName,
            u("{$actionName}_{$responseCode}_{$contentType}")->camel()->title(),
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return $this->name;
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->name}.php";
    }

    public function getTemplate(): string
    {
        return 'response.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'response';
    }
}