<?php

namespace App\Command;

use Symfony\Component\Config\Definition\Exception\Exception;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

class GenService extends AbstractExtension
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toMethodParam', [$this, 'toMethodParam']),
            new TwigFunction('propertyToMethodParam', [$this, 'propertyToMethodParam']),
            new TwigFunction('toRouteRequirement', [$this, 'toRouteRequirement']),
            new TwigFunction('getOperationParams', [$this, 'getOperationParams']),
            new TwigFunction('getParamConstraints', [$this, 'getParamConstraints']),
            new TwigFunction('getPropertyConstraints', [$this, 'getPropertyConstraints']),
            new TwigFunction('genResponses', [$this, 'genResponses']),
            new TwigFunction('toObjectSchemaClassName', [$this, 'toObjectSchemaClassName']),
            new TwigFunction('toParamArrayAnnotation', [$this, 'toParamArrayAnnotation']),
            new TwigFunction('toVariableName', [$this, 'toVariableName']),
            new TwigFunction('resolveRef', [$this, 'resolveRef']),
            new TwigFunction('getParamFromRequest', [$this, 'getParamFromRequest']),
        ];
    }

    public function generate(array $spec): void
    {
        foreach ($spec['paths'] as $route => $path) {
            if ($route[0] === '/') {
                $path = $this->resolveRef($spec, $path);
                foreach ($path as $method => $operation) {
                    if (in_array($method, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true)) {
                        $baseName = u($operation['operationId'])->camel()->title();
                        $controllerClassName = "{$baseName}Controller";
                        $handlerInterfaceName = "{$baseName}HandlerInterface";
                        file_put_contents(
                            __DIR__."/../Controller/{$controllerClassName}.php",
                            $this->twig->render(
                                'controller.php.twig',
                                [
                                    'className' => $controllerClassName,
                                    'handlerClassName' => $handlerInterfaceName,
                                    'operationName' => $baseName,
                                    'spec' => $spec,
                                    'operation' => $operation,
                                    'route' => $route,
                                    'method' => $method,
                                ],
                            ),
                        );
                        file_put_contents(
                            __DIR__."/../Controller/{$handlerInterfaceName}.php",
                            $this->twig->render(
                                'handler.php.twig',
                                [
                                    'interfaceName' => $handlerInterfaceName,
                                    'operationName' => $baseName,
                                    'spec' => $spec,
                                    'operation' => $operation,
                                    'route' => $route,
                                    'method' => $method,
                                ],
                            ),
                        );
                        // TODO Security field https://spec.openapis.org/oas/latest.html#fixed-fields-7
                    }
                }
            }
        }
    }

    public function getOperationParams(
        array $spec,
        string $route,
        string $method,
        array $in = ['path', 'query', 'header', 'cookie'],
    ): array {
        static $params = [];

        if (!isset($params[$route][$method])) {
            $pathParams = array_map(
                fn (array $param) => $this->resolveRef($spec, $param),
                $spec['paths'][$route]['parameters'] ?? [],
            );
            $operationParams = array_map(
                fn (array $param) => $this->resolveRef($spec, $param),
                $spec['paths'][$route][$method]['parameters'] ?? [],
            );
            $pathParams = array_combine(
                array_map(fn (array $param) => "{$param['in']}:{$param['name']}", $pathParams),
                $pathParams,
            );
            $operationParams = array_combine(
                array_map(fn (array $param) => "{$param['in']}:{$param['name']}", $operationParams),
                $operationParams,
            );
            $params[$route][$method] = array_values(array_merge($pathParams, $operationParams));
        }

        return array_filter(
            $params[$route][$method],
            fn (array $param) => in_array($param['in'], $in, true),
        );
    }

    public function toRouteRequirement(array $param): string
    {
        // TODO https://spec.openapis.org/oas/latest.html#style-values
        return sprintf(
            '\'%s\' => \'%s\',',
            $this->toVariableName($param),
            match ($param['schema']['type'] ?? 'mixed') {
                // TODO https://datatracker.ietf.org/doc/html/draft-bhutton-json-schema-validation-00#section-7.3
                'string' => $param['schema']['pattern'] ?? '[^:/?#[]@!$&\\\'()*+,;=]+',
                'number' => '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?',
                'integer' => '-?(0|[1-9]\d*)',
                'boolean' => 'true|false',
                default => '[^:/?#[]@!$&\\\'()*+,;=]+',
            }
        );
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

    public function toMethodParam(array $param): string
    {
        return sprintf(
            '%s%s $%s%s,',
            ($param['required'] ?? false) ? '' : '?',
            isset($param['schema']['type']) ? $this->toPhpType($param['schema']['type']) : 'mixed',
            $this->toVariableName($param),
            ($default = $this->getSchemaDefault($param['schema'])) !== null ? sprintf(' = %s', $default) : '',
        );
    }

    public function propertyToMethodParam(
        array $spec,
        string $parentSchemaName,
        array $schema,
        array $property,
        string $propertyName,
    ): string {
        $property = $this->resolveRef($spec, $property);
        return sprintf(
            '%s%s $%s%s',
            in_array($propertyName, $schema['required'] ?? [], true) || !isset($property['type']) ? '' : '?',
            // TODO array type
            match ($property['type'] ?? 'mixed') {
                'string' => 'string',
                'number' => 'float',
                'integer' => 'int',
                'boolean' => 'bool',
                'array' => 'array',
                'object' => $this->toObjectSchemaClassName($spec, $property, ucfirst("{$propertyName}{$parentSchemaName}")),
                'mixed' => 'mixed',
            },
            $propertyName,
            ($default = $this->getSchemaDefault($property)) !== null ? sprintf(' = %s', $default) : '',
        );
    }

    public function toParamArrayAnnotation(
        array $spec,
        string $parentSchemaName,
        array $schema,
        array $property,
        string $propertyName,
    ): string {
        $items = $this->resolveRef($spec, $property['items']);
        return sprintf(
            '@param %sarray<%s> $%s',
            in_array($propertyName, $schema['required'], true) ? '' : '?',
            match ($items['type']) {
                'string' => 'string',
                'number' => 'float',
                'integer' => 'int',
                'boolean' => 'bool',
                // TODO array elements type
                'array' => 'array',
                'object' => $this->toObjectSchemaClassName($spec, $items, ucfirst("{$propertyName}{$parentSchemaName}")),
            },
            $propertyName,
        );
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

    public function toObjectSchemaClassName(array $spec, array $schema, string $defaultClassName): string
    {
        $schemaClassName = $defaultClassName;

        // TODO https://spec.openapis.org/oas/latest.html#referenceObject
        if (isset($schema['$ref'])) {
            [,,, $schemaName] = explode('/', $schema['$ref']);
            $schemaClassName = "{$schemaName}Schema";
            $schema = $this->resolveRef($spec, $schema);
        }

        static $schemaClassNames = [];

        if (!isset($schemaClassNames[$schemaClassName])) {
            file_put_contents(
                __DIR__."/../Controller/{$schemaClassName}.php",
                $this->twig->render(
                    'schema.php.twig',
                    [
                        'spec' => $spec,
                        'schema' => $schema,
                        'className' => $schemaClassName,
                    ],
                ),
            );

            $schemaClassNames[$schemaClassName] = true;
        }

        return $schemaClassName;
    }

    public function getParamConstraints(array $param): array
    {
        return $this->getConstraints($param['required'] ?? false, $param['schema']);
    }

    public function getPropertyConstraints(array $schema, array $property, string $propertyName): array
    {
        return $this->getConstraints(in_array($propertyName, $schema['required'] ?? [], true), $property);
    }

    public function getConstraints(bool $required, array $schema): array
    {
        $constraints = [];

        if ($required) {
            $constraints[] = 'Assert\NotNull()';
        }

        if (isset($schema['format'])) {
            $formatClasses = $this->generateFormatClasses($schema['format']);
            $constraints[] = sprintf(
                '%s()',
                $formatClasses['constraintClassName'],
            );
        }

        if (isset($schema['pattern'])) {
            $constraints[] = sprintf(
                'Assert\Regex(\'/%s/\')',
                $schema['pattern'],
            );
        }

        if (isset($schema['minLength'])) {
            $constraints[] = sprintf(
                'Assert\Length(min: %d)',
                $schema['minLength'],
            );
        }

        if (isset($schema['maxLength'])) {
            $constraints[] = sprintf(
                'Assert\Length(max: %d)',
                $schema['maxLength'],
            );
        }

        if (isset($schema['minimum'])) {
            $constraints[] = sprintf(
                'Assert\%s(%d)',
                    $schema['exclusiveMinimum'] ?? false ? 'GreaterThan' : 'GreaterThanOrEqual',
                $schema['minimum'],
            );
        }

        if (isset($schema['maximum'])) {
            $constraints[] = sprintf(
                'Assert\%s(%d)',
                    $schema['exclusiveMaximum'] ?? false ? 'LessThan' : 'LessThanOrEqual',
                $schema['maximum'],
            );
        }

        if (isset($schema['enum'])) {
            $constraints[] = sprintf(
                'Assert\Choice([\'%s\'])',
                implode('\', \'', $schema['enum']),
            );
        }

        return $constraints;
    }

    public function getParamFromRequest(array $param): string
    {
        // TODO content field https://spec.openapis.org/oas/v3.1.0#fixed-fields-9
        return sprintf(
            '$%s = %s($request->%s->get(\'%s\', %s));',
            $this->toVariableName($param),
            ['number' => 'floatval', 'integer' => 'intval', 'boolean' => 'boolval'][$param['schema']['type']] ?? '',
            ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$param['in']],
            $param['name'],
            $this->getSchemaDefault($param['schema']) ?? 'null',
        );
    }

    public function resolveRef(array $spec, array $mixed): array
    {
        if (isset($mixed['$ref'])) {
            // TODO https://spec.openapis.org/oas/latest.html#referenceObject
            [,, $type, $name] = explode('/', $mixed['$ref']);
            return $spec['components'][$type][$name];
        }

        return $mixed;
    }

    public function getSchemaDefault(array $schema): ?string
    {
        if (isset($schema['type']) && isset($schema['default'])) {
            return match ($schema['type']) {
                'string' => sprintf('\'%s\'', str_replace('\'', '\\\'', $schema['default'])),
                default => $schema['default'],
            };
        }

        return null;
    }

    public function toVariableName(array $param): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$param['in']],
            ucfirst($param['name']),
        );
    }

    private function generateFormatClasses(string $format): array
    {
        static $formats = [];

        if (!isset($formats[$format])) {
            $baseName = u($format)->camel()->title();
            $validatorClassName = "{$baseName}Validator";
            $definitionInterfaceName = "{$baseName}Definition";
            file_put_contents(
                __DIR__."/../Controller/{$baseName}.php",
                $this->twig->render(
                    'format-constraint.php.twig',
                    [
                        'className' => $baseName,
                    ],
                ),
            );
            file_put_contents(
                __DIR__."/../Controller/{$validatorClassName}.php",
                $this->twig->render(
                    'format-validator.php.twig',
                    [
                        'className' => $validatorClassName,
                        'definitionInterfaceName' => $definitionInterfaceName,
                    ],
                ),
            );
            file_put_contents(
                __DIR__."/../Controller/{$definitionInterfaceName}.php",
                $this->twig->render(
                    'format-definition.php.twig',
                    [
                        'className' => $definitionInterfaceName,
                    ],
                ),
            );

            $formats[$format] = [
                'constraintClassName' => $baseName,
                'validatorClassName' => $validatorClassName,
                'definitionInterfaceName' => $definitionInterfaceName,
            ];
        }

        return $formats[$format];
    }
}