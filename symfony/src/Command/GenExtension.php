<?php

namespace App\Command;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

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
            new TwigFilter('toRequestPayloadClassName', [$this, 'toRequestPayloadClassName']),
            new TwigFilter('toPhpType', [$this, 'toPhpType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toPhpParam', [$this, 'toPhpParam']),
            new TwigFunction('toRouteRequirement', [$this, 'toRouteRequirement']),
            new TwigFunction('getOperationParams', [$this, 'getOperationParams']),
            new TwigFunction('getParamConstraints', [$this, 'getParamConstraints']),
            new TwigFunction('genObjectSchema', [$this, 'genObjectSchema']),
            new TwigFunction('genResponses', [$this, 'genResponses']),
            new TwigFunction('toObjectSchemaClassName', [$this, 'toObjectSchemaClassName']),
            new TwigFunction('resolveRef', [$this, 'resolveRef']),
        ];
    }

    public function toControllerClassName(string $operationId): string
    {
        return u($operationId)->camel()->title().'Controller';
    }

    public function toHandlerClassName(string $operationId): string
    {
        return u($operationId)->camel()->title().'Handler';
    }

    public function toRequestPayloadClassName(string $operationId): string
    {
        return u($operationId)->camel()->title().'RequestPayload';
    }

    public function toPhpType(string $type): string
    {
        return [
            'string' => 'string',
            'number' => 'float',
            'integer' => 'int',
            'boolean' => 'bool',
            'array' => 'array',
        ][$type];
    }

    public function toSchemaClassName($ref): string
    {
        [,, $type, $class] = explode('/', $ref);

        return $class . ucfirst(substr($type, 0, -1));
    }

    public function toPhpParam(array $spec, array $param): string
    {
        $param = $this->resolveRef($spec, $param);

        return sprintf(
            '%s%s $%s,',
            ($param['required'] ?? false) ? '' : '?',
            isset($param['schema']['type']) ? $this->toPhpType($param['schema']['type']) : 'mixed',
            $param['name'],
        );
    }

    public function toRouteRequirement(array $spec, array $param): string
    {
        $param = $this->resolveRef($spec, $param);

        return sprintf(
            '\'%s\' => \'%s\',',
            $param['name'],
            match ($param['schema']['type'] ?? 'mixed') {
                'string' => '.+',
                'number' => '\d+',
                'integer' => '\d+',
                'boolean' => 'true|false',
                'array' => '.+',
                'mixed' => '.+',
            }
        );
    }

    public function getOperationParams(array $spec, string $route, string $method, array $in = ['path', 'query', 'header', 'cookie']): array
    {
        return array_filter(
            array_merge(
                $spec['paths'][$route]['parameters'] ?? [],
                $spec['paths'][$route][$method]['parameters'] ?? [],
            ),
            fn (array $param) => in_array($this->resolveRef($spec, $param)['in'], $in, true),
        );
    }

    public function genObjectSchema(array $spec, array $schema, string $name): void
    {
        $schema = $this->resolveRef($spec, $schema);

        $template = $this->twig->render('schema.php.twig', ['spec' => $spec, 'schema' => $schema, 'name' => $name]);
        file_put_contents(__DIR__.'/../Controller/'.$name.'.php', $template);
    }

    public function genResponses(array $spec, array $operation): string
    {
        $responseNames = [];
        foreach ($operation['responses'] ?? [] as $code => $response) {
            $response = $this->resolveRef($spec, $response);
            foreach ($response['content'] ?? ['empty' => []] as $type => $content) {
                $responseNames[] = $responseName = $this->toResponseName($operation['operationId'], $code, $type);
                $template = $this->twig->render('response.php.twig', ['spec' => $spec, 'code' => $code, 'className' => $responseName, 'type' => $type, 'content' => $content]);
                file_put_contents(__DIR__ . '/../Controller/' . $responseName . '.php', $template);
            }
        }

        return implode('|', $responseNames);
    }

    public function toResponseName(string $operationId, string $code, string $type): string
    {
        return u($operationId)->camel()->title().$code.u($type)->camel()->title().'Response';
    }

    public function toObjectSchemaClassName(array $schema, string $default): string
    {
        if (isset($schema['$ref'])) {
            [,,, $name] = explode('/', $schema['$ref']);
            return "{$name}Schema";
        }

        return $default;
    }

    public function getParamConstraints(array $spec, array $param): array
    {
        $param = $this->resolveRef($spec, $param);

        $constraints = [];

        if ($param['required'] ?? false) {
            $constraints[] = 'new Assert\NotNull(),';
        }

        return $constraints;
    }

    public function resolveRef(array $spec, array $mixed): array
    {
        if (isset($mixed['$ref'])) {
            [,, $type, $name] = explode('/', $mixed['$ref']);
            return $spec['components'][$type][$name];
        }

        return $mixed;
    }
}