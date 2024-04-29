<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Switch_;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Header;
use Zol\Ogen\OpenApi\OpenApi;
use Zol\Ogen\OpenApi\Parameter;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\RequestBody;
use Zol\Ogen\OpenApi\Response;
use Zol\Ogen\OpenApi\Schema;
use function Symfony\Component\String\u;

class Bundle implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $rawName,
        string $packageName,
        string $namespace,
        OpenApi $openApi,
    ): self {
        return new self(
            u($rawName)->camel()->title(),
            $namespace,
            $formats = self::buildFormats($namespace, $openApi),
            self::buildModels($namespace, $openApi->components),
            $api = Api::build($namespace, $openApi),
            RoutesConfig::build($namespace, $api),
            ServicesConfig::build($namespace, $api, $formats),
            new ComposerJson($packageName, $namespace),
        );
    }

    /**
     * @param array<string, Format> $formats
     * @param array<Model> $models
     */
    private function __construct(
        private readonly string $name,
        private readonly string $namespace,
        private readonly array $formats,
        private readonly array $models,
        private readonly Api $api,
        private readonly RoutesConfig $routesConfig,
        private readonly ServicesConfig $servicesConfig,
        private readonly ComposerJson $composerJson,
    ) {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [
            $this,
            $this->routesConfig,
            $this->servicesConfig,
            $this->composerJson,
        ];

        foreach ($this->formats as $format) {
            foreach ($format->getFiles() as $file) {
                $files[] = $file;
            }
        }

        foreach ($this->models as $model) {
            $files[] = $model;
        }

        foreach ($this->api->getFiles() as $file){
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @return array<string, Format>
     * @throws Exception
     */
    private static function buildFormats(string $namespace, OpenApi $openApi): array
    {
        $rawFormatNames = [];

        $addSchemaFormats = function (Reference|Schema $schema) use (&$addSchemaFormats, &$rawFormatNames) {
            if ($schema instanceof Schema) {
                if ($schema->format !== null) {
                    $rawFormatNames[$schema->format] = null;
                }
                foreach ($schema->properties ?? [] as $property) {
                    $addSchemaFormats($property);
                }
                if ($schema->items !== null) {
                    $addSchemaFormats($schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas ?? [] as $schema) {
            $addSchemaFormats($schema);
        }
        foreach ($openApi->components->parameters ?? [] as $parameter) {
            if ($parameter->schema === null) {
                throw new Exception('Parameter objects without schema are not supported.');
            }
            $addSchemaFormats($parameter->schema);
        }
        foreach ($openApi->components->requestBodies ?? [] as $requestBody) {
            foreach ($requestBody->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('Mediatype objects without schema are not supported.');
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->responses ?? [] as $response) {
            foreach ($response->headers as $header) {
                if ($header instanceof Header) {
                    if ($header->schema === null) {
                        throw new Exception('Header objects without schema are not supported.');
                    }
                    $addSchemaFormats($header->schema);
                }
            }
            foreach ($response->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('MediaType objects without schema are not supported.');
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->headers ?? [] as $header) {
            if ($header->schema === null) {
                throw new Exception('Header objects without schema are not supported.');
            }
            $addSchemaFormats($header->schema);
        }
        foreach ($openApi?->paths->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->parameters as $parameter) {
                if ($parameter instanceof Parameter) {
                    if ($parameter->schema === null) {
                        throw new Exception('Parameter objects without schema are not supported.');
                    }
                    $addSchemaFormats($parameter->schema);
                }
            }
            foreach ($pathItem->operations as $operation) {
                foreach ($operation->parameters as $parameter) {
                    if ($parameter instanceof Parameter) {
                        if ($parameter->schema === null) {
                            throw new Exception('Parameter objects without schema are not supported.');
                        }
                        $addSchemaFormats($parameter->schema);
                    }
                }
                if ($operation->requestBody instanceof RequestBody) {
                    foreach ($operation->requestBody->content as $mediaType) {
                        if ($mediaType->schema === null) {
                            throw new Exception('MediaType objects without schema are not supported.');
                        }
                        $addSchemaFormats($mediaType->schema);
                    }
                }
                foreach ($operation?->responses->responses ?? [] as $response) {
                    if ($response instanceof Response) {
                        foreach ($response->headers as $header) {
                            if ($header instanceof Header) {
                                if ($header->schema === null) {
                                    throw new Exception('Header objects without schema are not supported.');
                                }
                                $addSchemaFormats($header->schema);
                            }
                        }
                        foreach ($response->content as $mediaType) {
                            if ($mediaType->schema === null) {
                                throw new Exception('Mediatype objects without schema are not supported.');
                            }
                            $addSchemaFormats($mediaType->schema);
                        }
                    }
                }
            }
        }

        $formats = [];
        foreach ($rawFormatNames as $rawFormatName => $_) {
            $formats[$rawFormatName] = Format::build($namespace, $rawFormatName);
        }

        return $formats;
    }

    /**
     * @return array<Model>
     *
     * @throws Exception
     */
    private static function buildModels(string $namespace, ?Components $components): array
    {
        $models = [];

        /**
         * @throws Exception
         */
        $addModels = function(string $rawName, Reference|Schema $schema) use (&$addModels, &$models, $namespace, $components) {
            if ($schema instanceof Reference) {
                if ($components === null || !isset($components->schemas[$schema->getName()])) {
                    throw new Exception('Reference not found in schemas components.');
                }
                $schema = $components->schemas[$rawName = $schema->getName()];
            }
            if (!isset($models[$rawName])) {
                $type = TypeFactory::build('', $schema, $components);
                if ($type instanceof ObjectType) {
                    if (!($schema->extensions['x-raw'] ?? false)) {
                        $models[$rawName] = Model::build(
                            $namespace,
                            "{$namespace}\Model",
                            "src/Model",
                            $rawName,
                            $schema,
                            $components,
                            true,
                        );
                        foreach ($schema->properties as $propertyName => $property) {
                            $addModels("{$rawName}_{$propertyName}", $property);
                        }
                    }
                } elseif ($type instanceof ArrayType) {
                    if ($schema->items === null) {
                        throw new Exception('Schema objects of array type without items attribute are not supported.');
                    }
                    $addModels($rawName, $schema->items);
                }
            }
        };

        foreach ($components->schemas ?? [] as $rawName => $schema) {
            $addModels($rawName, $schema);
        }

        return $models;
    }

    public function getClassName(): string
    {
        return "{$this->name}Bundle";
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getServiceName(): string
    {
        return u($this->name)->snake();
    }

    /**
     * @return array<Aggregate>
     */
    public function getAggregates(): array
    {
        return $this->api->getAggregates();
    }

    /**
     * @return array<string, Format>
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    public function getFolder(): string
    {
        return 'src';
    }

    public function getName(): string
    {
        return "{$this->name}Bundle.php";
    }

    public function getTemplate(): string
    {
        return 'bundle.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'bundle';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $buildMethod = $f->method('build')
            ->makePublic()
            ->setReturnType('void')
            ->addParam($f->param('container')->setType('ContainerBuilder'))
            ->addStmt($f->staticCall('parent', 'build', [$f->var('container')]))
            ->addStmt($f->methodCall($f->var('container'), 'addCompilerPass', [
                new New_($f->class('')->implement('CompilerPassInterface')
                    ->addStmt($f->method('process')
                        ->makePublic()
                        ->addParam($f->param('container')->setType('ContainerBuilder'))
                        ->setReturnType('void')
                        ->addStmt(new Foreach_($f->methodCall($f->var('container'), 'findTaggedServiceIds', [sprintf('%s.handler', u($this->name)->snake())]), $f->var('tags'), ['keyVar' => $f->var('id'), 'stmts' => [
                            new Foreach_($f->var('tags'), $f->var('tag'), ['stmts' => [
                                new Switch_(new ArrayDimFetch($f->var('tag'), $f->val('controller')), array_map(
                                    fn (Aggregate $aggregate) => new Case_($f->val($aggregate->getTag()), [
                                        new Expression($f->methodCall($f->methodCall($f->var('container'), 'findDefinition', [sprintf('%s\%s', $aggregate->getController()->getNamespace(), $aggregate->getController()->getClassName())]), 'addMethodCall', [$f->val('setHandler'), new Array_([new \PhpParser\Node\ArrayItem($f->new('Reference', [$f->var('id')]))])])),
                                        new Break_(),
                                    ]),
                                    $this->getAggregates(),
                                )),
                            ]]),
                        ]]))
                        ->addStmt(new Foreach_($f->methodCall($f->var('container'), 'findTaggedServiceIds', [sprintf('%s.format_definition', u($this->name)->snake())]), $f->var('tags'), ['keyVar' => $f->var('id'), 'stmts' => [
                            new Foreach_($f->var('tags'), $f->var('tag'), ['stmts' => [
                                new Switch_(new ArrayDimFetch($f->var('tag'), $f->val('format')), array_map(
                                    fn (string $formatName) => new Case_($f->val($formatName), [
                                        new Expression($f->methodCall($f->methodCall($f->var('container'), 'findDefinition', [sprintf('%s\%s', $this->getFormats()[$formatName]->getValidator()->getNamespace(), $this->getFormats()[$formatName]->getValidator()->getClassName())]), 'addMethodCall', [$f->val('setFormatDefinition'), new Array_([new \PhpParser\Node\ArrayItem($f->new('Reference', [$f->var('id')]))])])),
                                        new Break_(),
                                    ]),
                                    array_keys($this->getFormats()),
                                )),
                            ]]),
                        ]])),
                    )->getNode(),
                ),
            ]));

        $loadExtensionMethod = $f->method('loadExtension')
            ->makePublic()
            ->addParam($f->param('config')->setType('array'))
            ->addParam($f->param('container')->setType('ContainerConfigurator'))
            ->addParam($f->param('builder')->setType('ContainerBuilder'))
            ->setReturnType('void')
            ->setDocComment(<<<'COMMENT'
                /**
                 * @param array<mixed> $config
                 */
                COMMENT
            )
            ->addStmt($f->methodCall($f->var('container'), 'import', [$f->val('../config/services.yaml')]));

        $class = $f->class("{$this->name}Bundle")
            ->extend('AbstractBundle')
            ->addStmt($buildMethod)
            ->addStmt($loadExtensionMethod);

        $namespace = $f->namespace($this->namespace)
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\ContainerBuilder'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Reference'))
            ->addStmt($f->use('Symfony\Component\HttpKernel\Bundle\AbstractBundle'))
            ->addStmt($class);

        return $namespace->getNode();
    }
}