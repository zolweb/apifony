<?php

namespace App\Command;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GenExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('toControllerClassName', [$this, 'toControllerClassName']),
            new TwigFilter('toHandlerClassName', [$this, 'toHandlerClassName']),
            new TwigFilter('toPhpType', [$this, 'toPhpType']),
            new TwigFilter('toSchemaClassName', [$this, 'toSchemaClassName']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getOperationParams', [$this, 'getOperationParams']),
            new TwigFunction('genSchema', [$this, 'genSchema']),
        ];
    }

    public function toControllerClassName(string $operationId): string
    {
        return ucfirst($operationId).'Controller';
    }

    public function toHandlerClassName(string $operationId): string
    {
        return ucfirst($operationId).'Handler';
    }

    public function toPhpType(string $type): string
    {
        return ['string' => 'string', 'integer' => 'int', 'array' => 'array', 'boolean' => 'bool'][$type];
    }

    public function toSchemaClassName($ref): string
    {
        [,, $type, $class] = explode('/', $ref);

        return $class . ucfirst(substr($type, 0, -1));
    }

    public function getOperationParams(array $spec, string $route, string $method, ?string $in = null): array
    {
        return array_filter(
            array_merge(
                $spec['paths'][$route]['parameters'] ?? [],
                $spec['paths'][$route][$method]['parameters'] ?? [],
            ),
            fn (array $param) => $param['in'] === $in,
        );
    }

    public function genSchema(array $spec, string $ref): void
    {
        [,,, $name] = explode('/', $ref);
        $component = $spec['components']['schemas'][$name];
        if ($component['type'] === 'object') {
            $template = $this->twig->render('schema.php.twig', ['spec' => $spec, 'name' => $name]);
            file_put_contents(__DIR__.'/../Controller/'.$name.'Schema.php', $template);
        }
    }
}