<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\PrettyPrinter\Standard;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Header;
use Zol\Apifony\OpenApi\OpenApi;
use Zol\Apifony\OpenApi\Parameter;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\RequestBody;
use Zol\Apifony\OpenApi\Response;
use Zol\Apifony\OpenApi\Schema;

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
            $name = Naming::forClass($rawName),
            $namespace,
            $formats = self::buildFormats($namespace, $name, $openApi),
            $models = self::buildModels($namespace, $openApi->components),
            $api = Api::build($namespace, $name, $openApi, $models),
            RoutesConfig::build($namespace, $api),
            ServicesConfig::build($namespace, $api, $formats),
            new ComposerJson($packageName, $namespace),
            new ConstraintValidatorFactory($namespace),
        );
    }

    /**
     * @param array<string, Format> $formats
     * @param list<Model>           $models
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
        private readonly ConstraintValidatorFactory $constraintValidatorFactory,
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
            $this->constraintValidatorFactory,
        ];

        foreach ($this->formats as $format) {
            foreach ($format->getFiles() as $file) {
                $files[] = $file;
            }
        }

        foreach ($this->models as $model) {
            $files[] = $model;
        }

        foreach ($this->api->getFiles() as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @return array<string, Format>
     *
     * @throws Exception
     */
    private static function buildFormats(string $namespace, string $name, OpenApi $openApi): array
    {
        $rawFormatNames = [];

        $addSchemaFormats = static function (Reference|Schema $schema) use (&$addSchemaFormats, &$rawFormatNames): void {
            if ($schema instanceof Schema) {
                if ($schema->format !== null) {
                    $rawFormatNames[$schema->format] = null;
                }
                foreach ($schema->properties as $property) {
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
                throw new Exception('Parameter objects without schema are not supported.', $parameter->path);
            }
            $addSchemaFormats($parameter->schema);
        }
        foreach ($openApi->components->requestBodies ?? [] as $requestBody) {
            foreach ($requestBody->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('Mediatype objects without schema are not supported.', $mediaType->path);
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->responses ?? [] as $response) {
            foreach ($response->headers as $header) {
                if ($header instanceof Header) {
                    if ($header->schema === null) {
                        throw new Exception('Header objects without schema are not supported.', $header->path);
                    }
                    $addSchemaFormats($header->schema);
                }
            }
            foreach ($response->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('MediaType objects without schema are not supported.', $mediaType->path);
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->headers ?? [] as $header) {
            if ($header->schema === null) {
                throw new Exception('Header objects without schema are not supported.', $header->path);
            }
            $addSchemaFormats($header->schema);
        }
        foreach ($openApi?->paths->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->parameters as $parameter) {
                if ($parameter instanceof Parameter) {
                    if ($parameter->schema === null) {
                        throw new Exception('Parameter objects without schema are not supported.', $parameter->path);
                    }
                    $addSchemaFormats($parameter->schema);
                }
            }
            foreach ($pathItem->operations as $operation) {
                foreach ($operation->parameters as $parameter) {
                    if ($parameter instanceof Parameter) {
                        if ($parameter->schema === null) {
                            throw new Exception('Parameter objects without schema are not supported.', $parameter->path);
                        }
                        $addSchemaFormats($parameter->schema);
                    }
                }
                if ($operation->requestBody instanceof RequestBody) {
                    foreach ($operation->requestBody->content as $mediaType) {
                        if ($mediaType->schema === null) {
                            throw new Exception('MediaType objects without schema are not supported.', $mediaType->path);
                        }
                        $addSchemaFormats($mediaType->schema);
                    }
                }
                foreach ($operation?->responses->responses ?? [] as $response) {
                    if ($response instanceof Response) {
                        foreach ($response->headers as $header) {
                            if ($header instanceof Header) {
                                if ($header->schema === null) {
                                    throw new Exception('Header objects without schema are not supported.', $header->path);
                                }
                                $addSchemaFormats($header->schema);
                            }
                        }
                        foreach ($response->content as $mediaType) {
                            if ($mediaType->schema === null) {
                                throw new Exception('Mediatype objects without schema are not supported.', $mediaType->path);
                            }
                            $addSchemaFormats($mediaType->schema);
                        }
                    }
                }
            }
        }

        $formats = [];
        foreach ($rawFormatNames as $rawFormatName => $_) {
            $formats[$rawFormatName] = Format::build($namespace, $name, $rawFormatName);
        }

        return $formats;
    }

    /**
     * @return list<Model>
     *
     * @throws Exception
     */
    private static function buildModels(string $namespace, ?Components $components): array
    {
        $collector = ModelCollector::forComponents($namespace, $components);

        foreach ($components->schemas ?? [] as $rawName => $schema) {
            $collector->collect($rawName, $schema);
        }

        return $collector->getModels();
    }

    public function getFolder(): string
    {
        return 'src';
    }

    public function getName(): string
    {
        return "{$this->name}Bundle.php";
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $buildMethod = $f->method('build')
            ->makePublic()
            ->setReturnType('void')
            ->addParam($f->param('container')->setType('ContainerBuilder'))
            ->addStmt($f->staticCall('parent', 'build', [$f->var('container')]))
            ->addStmts($this->api->getAutoconfiguration())
            ->addStmts(
                array_filter(
                    array_map(
                        static fn (Format $format) => $format->getAutoconfiguration(),
                        $this->formats,
                    ),
                ),
            )
            ->addStmt($f->methodCall($f->var('container'), 'addCompilerPass', [
                new New_(
                    new Class_(null, ['implements' => [new Name('CompilerPassInterface')], 'stmts' => [
                        $f->method('process')
                            ->makePublic()
                            ->addParam($f->param('container')->setType('ContainerBuilder'))
                            ->setReturnType('void')
                            ->addStmt(new Foreach_($f->methodCall($f->var('container'), 'findTaggedServiceIds', [$handlerTag = \sprintf('%s.handler', Naming::forServiceId($this->name))]), $f->var('tags'), ['keyVar' => $f->var('id'), 'stmts' => [
                                new Foreach_($f->var('tags'), $f->var('tag'), ['stmts' => [
                                    $this->buildTagAttributeGuard($handlerTag, 'controller'),
                                    new Switch_(new ArrayDimFetch($f->var('tag'), $f->val('controller')), $this->api->getCases()),
                                ]]),
                            ]]))
                            ->addStmt(new Foreach_($f->methodCall($f->var('container'), 'findTaggedServiceIds', [$formatTag = \sprintf('%s.format_definition', Naming::forServiceId($this->name))]), $f->var('tags'), ['keyVar' => $f->var('id'), 'stmts' => [
                                new Foreach_($f->var('tags'), $f->var('tag'), ['stmts' => [
                                    $this->buildTagAttributeGuard($formatTag, 'format'),
                                    new Switch_(new ArrayDimFetch($f->var('tag'), $f->val('format')), array_map(
                                        static fn (Format $format) => $format->getCase(),
                                        $this->formats,
                                    )),
                                ]]),
                            ]]))->getNode(),
                    ]]),
                ),
            ]))
        ;

        $loadExtensionMethod = $f->method('loadExtension')
            ->makePublic()
            ->addParam($f->param('config')->setType('array'))
            ->addParam($f->param('container')->setType('ContainerConfigurator'))
            ->addParam($f->param('builder')->setType('ContainerBuilder'))
            ->setReturnType('void')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * @param array<mixed> $config
                     */
                    COMMENT
            )
            ->addStmt($f->methodCall($f->var('container'), 'import', [$f->val('../config/services.yaml')]))
        ;

        $class = $f->class("{$this->name}Bundle")
            ->extend('AbstractBundle')
            ->addStmt($buildMethod)
            ->addStmt($loadExtensionMethod)
        ;

        $namespace = $f->namespace($this->namespace)
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\ContainerBuilder'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator'))
            ->addStmt($f->use('Symfony\Component\DependencyInjection\Reference'))
            ->addStmt($f->use('Symfony\Component\HttpKernel\Bundle\AbstractBundle'))
            ->addStmts($this->api->getUses())
            ->addStmts(
                array_filter(
                    array_map(
                        static fn (Format $format) => $format->getDefinitionUse(),
                        $this->formats,
                    ),
                ),
            )
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }

    private function buildTagAttributeGuard(string $tagName, string $attribute): If_
    {
        $f = new BuilderFactory();

        return new If_(
            new BooleanOr(
                new BooleanNot($f->funcCall('\is_array', [$f->var('tag')])),
                new BooleanNot($f->funcCall('\array_key_exists', [$f->val($attribute), $f->var('tag')])),
            ),
            ['stmts' => [
                new Expression(new Throw_($f->new('\InvalidArgumentException', [
                    $f->funcCall('\sprintf', [
                        $f->val(\sprintf('Service "%%s" tagged as "%s" must define the "%s" tag attribute.', $tagName, $attribute)),
                        $f->var('id'),
                    ]),
                ]))),
            ]],
        );
    }
}
