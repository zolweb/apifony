<?php

namespace App\Command;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function Symfony\Component\String\u;

class GenService extends AbstractExtension
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('toRequestPayloadClassName', [$this, 'toRequestPayloadClassName']),
            new TwigFilter('toPhpType', [$this, 'toPhpType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toMethodParam', [$this, 'toMethodParam']),
            new TwigFunction('toRouteRequirement', [$this, 'toRouteRequirement']),
            new TwigFunction('getOperationParams', [$this, 'getOperationParams']),
            new TwigFunction('getParamConstraints', [$this, 'getParamConstraints']),
            new TwigFunction('genObjectSchema', [$this, 'genObjectSchema']),
            new TwigFunction('genResponses', [$this, 'genResponses']),
            new TwigFunction('toObjectSchemaClassName', [$this, 'toObjectSchemaClassName']),
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

    public function toMethodParam(array $param): string
    {
        return sprintf(
            '%s%s $%s%s,',
            ($param['required'] ?? false) ? '' : '?',
            isset($param['schema']['type']) ? $this->toPhpType($param['schema']['type']) : 'mixed',
            $this->toVariableName($param),
            ($default = $this->getParamDefault($param)) !== null ? sprintf(' = %s', $default) : '',
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

    public function getParamConstraints(array $param): array
    {
        $constraints = [];

        if ($param['required'] ?? false) {
            $constraints[] = 'new Assert\NotNull(),';
        }

        if (isset($param['schema']['format'])) {
            $formatClasses = $this->generateFormatClasses($param['schema']['format']);
            $constraints[] = sprintf(
                'new %s(),',
                $formatClasses['constraintClassName'],
            );
        }

        if (isset($param['schema']['pattern'])) {
            $constraints[] = sprintf(
                'new Assert\Regex(\'/%s/\'),',
                $param['schema']['pattern'],
            );
        }

        if (isset($param['schema']['minLength'])) {
            $constraints[] = sprintf(
                'new Assert\Length(min: %d),',
                $param['schema']['minLength'],
            );
        }

        if (isset($param['schema']['maxLength'])) {
            $constraints[] = sprintf(
                'new Assert\Length(max: %d),',
                $param['schema']['maxLength'],
            );
        }

        if (isset($param['schema']['minimum'])) {
            $constraints[] = sprintf(
                'new Assert\%s(%d),',
                $param['schema']['exclusiveMinimum'] ?? false ? 'GreaterThan' : 'GreaterThanOrEqual',
                $param['schema']['minimum'],
            );
        }

        if (isset($param['schema']['maximum'])) {
            $constraints[] = sprintf(
                'new Assert\%s(%d),',
                    $param['schema']['exclusiveMaximum'] ?? false ? 'LessThan' : 'LessThanOrEqual',
                $param['schema']['maximum'],
            );
        }

        if (isset($param['schema']['enum'])) {
            $constraints[] = sprintf(
                'new Assert\Choice([\'%s\']),',
                implode('\', \'', $param['schema']['enum']),
            );
        }

        return $constraints;
    }

    public function getParamFromRequest(array $param): string
    {
        return sprintf(
            '$%s = %s($request->%s->get(\'%s\', %s));',
            $this->toVariableName($param),
            ['number' => 'floatval', 'integer' => 'intval', 'boolean' => 'boolval'][$param['schema']['type']] ?? '',
            ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$param['in']],
            $param['name'],
            $this->getParamDefault($param),
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

    public function getParamDefault(array $param): string
    {
        if (isset($param['schema']['type']) && isset($param['schema']['default'])) {
            return match ($param['schema']['type']) {
                'string' => sprintf('\'%s\'', str_replace('\'', '\\\'', $param['schema']['default'])),
                default => $param['schema']['default'],
            };
        }

        return 'null';
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