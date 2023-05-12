<?php

namespace App\Command;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
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
            // new TwigFunction('toMethodParam', [$this, 'toMethodParam']),
            // new TwigFunction('propertyToMethodParam', [$this, 'propertyToMethodParam']),
            // new TwigFunction('toRouteRequirement', [$this, 'toRouteRequirement']),
            // new TwigFunction('getOperationParams', [$this, 'getOperationParams']),
            // new TwigFunction('getParamConstraints', [$this, 'getParamConstraints']),
            // new TwigFunction('getPropertyConstraints', [$this, 'getPropertyConstraints']),
            // new TwigFunction('genResponses', [$this, 'genResponses']),
            // new TwigFunction('toObjectSchemaClassName', [$this, 'toObjectSchemaClassName']),
            // new TwigFunction('toParamArrayAnnotation', [$this, 'toParamArrayAnnotation']),
            // new TwigFunction('toVariableName', [$this, 'toVariableName']),
            // new TwigFunction('getParamFromRequest', [$this, 'getParamFromRequest']),
        ];
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function generate(array $spec): void
    {
        foreach ((new Specification($spec))->getFiles() as $fileName => $file) {
            file_put_contents(
                __DIR__."/../Controller/{$fileName}.php",
                $this->twig->render($file['template'], $file['params']));
        }
    }

    // $resolveRefs = function (array &$parentNode) use (&$resolveRefs, $spec): void {
    //     foreach ($parentNode as &$childNode) {
    //         if (is_array($childNode)) {
    //             if (isset($childNode['$ref'])) {
    //                 // TODO https://spec.openapis.org/oas/latest.html#referenceObject
    //                 [, , $type, $name] = explode('/', $childNode['$ref']);
    //                 $childNode = $spec['components'][$type][$name];
    //                 $childNode['x-ref'] = ['name' => $name];
    //             }

    //             $resolveRefs($childNode);
    //         }
    //     }
    // };

    // $resolveRefs($spec);

    public function getOperationParams(
        array $path,
        string $method,
        array $in = ['path', 'query', 'header', 'cookie'],
    ): array {
        $pathParams = $path['parameters'] ?? [];
        $operationParams = $path[$method]['parameters'] ?? [];
        $pathParams = array_combine(
            array_map(fn (array $param) => "{$param['in']}:{$param['name']}", $pathParams),
            $pathParams,
        );
        $operationParams = array_combine(
            array_map(fn (array $param) => "{$param['in']}:{$param['name']}", $operationParams),
            $operationParams,
        );

        $params = array_filter(
            array_values(array_merge($pathParams, $operationParams)),
            fn (array $param) => in_array($param['in'], $in, true),
        );

        foreach ($params as $param) {
            if (($param['schema']['type'] ?? '') === 'array') {
                throw new \RuntimeException('Param of array type are not supported yet.');
            }
        }

        usort(
            $params,
            static fn (array $param1, array $param2) =>
            ((int)isset($param1['schema']['default']) - (int)isset($param2['schema']['default'])) ?:
                strcmp($param1['name'], $param2['name']),
        );

        return $params;
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

    public function toMethodParam(array $param): string
    {
        return sprintf(
            '%s%s $%s%s,',
            ($param['required'] ?? false) ? '' : '?',
            isset($param['schema']['type']) ?
                ['string' => 'string', 'number' => 'float', 'integer' => 'int', 'boolean' => 'bool', 'array' => 'array'][$param['schema']['type']] :
                'mixed',
            $this->toVariableName($param),
            ($default = $this->getSchemaDefault($param['schema'])) !== null ? sprintf(' = %s', $default) : '',
        );
    }

    public function propertyToMethodParam(
        string $parentSchemaName,
        array $schema,
        array $property,
        string $propertyName,
    ): string {
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
                'object' => $this->toObjectSchemaClassName($property, ucfirst("{$propertyName}{$parentSchemaName}")),
                'mixed' => 'mixed',
            },
            $propertyName,
            ($default = $this->getSchemaDefault($property)) !== null ? sprintf(' = %s', $default) : '',
        );
    }

    public function genResponses(array $operation): string
    {
        $responseNames = [];
        foreach ($operation['responses'] ?? [] as $code => $response) {
            foreach ($response['content'] ?? ['empty' => []] as $type => $content) {
                $responseNames[] = $responseName = sprintf(
                    '%s%s%sResponse',
                    u($operation['operationId'])->camel()->title(),
                    $code,
                    u($type)->camel()->title(),
                );
                file_put_contents(
                    __DIR__.'/../Controller/'.$responseName.'.php',
                    $this->twig->render(
                        'response.php.twig',
                        [
                            'code' => $code,
                            'className' => $responseName,
                            'type' => $type,
                            'content' => $content,
                        ],
                    ),
                );
            }
        }

        return implode('|', $responseNames);
    }

    public function toObjectSchemaClassName(array $schema, string $defaultClassName): string
    {
        $className = $defaultClassName;

        if (isset($schema['x-ref'])) {
            $className = "{$schema['x-ref']['name']}Schema";
        }

        static $classNames = [];

        if (!isset($classNames[$className])) {
            file_put_contents(
                __DIR__."/../Controller/{$className}.php",
                $this->twig->render(
                    'schema.php.twig',
                    [
                        'schema' => $schema,
                        'className' => $className,
                    ],
                ),
            );

            $classNames[$className] = true;
        }

        return $className;
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

        if (isset($schema['minItems'])) {
            $constraints[] = sprintf(
                'Assert\Count(min: %d)',
                $schema['minItems'],
            );
        }

        if (isset($schema['maxItems'])) {
            $constraints[] = sprintf(
                'Assert\Count(max: %d)',
                $schema['maxItems'],
            );
        }

        if (isset($schema['uniqueItems'])) {
            $constraints[] = 'Assert\Unique()';
        }

        if (($schema['type'] ?? 'mixed') === 'object') {
            $constraints[] = 'Assert\Valid()';
        }

        if (($schema['type'] ?? 'mixed') === 'array') {
            $constraints[] = sprintf(
                "Assert\All([%s\n\t\t])",
                implode(
                    '',
                    array_map(
                        static fn (string $c) => "\n\t\t\tnew $c,",
                        $this->getConstraints($required, $schema['items'] ?? []),
                    ),
                ),
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

    public function getSchemaDefault(array $schema): ?string
    {
        if (isset($schema['type']) && isset($schema['default'])) {
            return match ($schema['type']) {
                'string' => sprintf('\'%s\'', str_replace('\'', '\\\'', $schema['default'])),
                'boolean' => $schema['default'] ? 'true' : 'false',
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
