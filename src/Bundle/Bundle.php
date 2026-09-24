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
use Zol\Apifony\OpenApi\Exception as OpenApiException;
use Zol\Apifony\OpenApi\OpenApi;
use Zol\Apifony\Resolved\Components;
use Zol\Apifony\Resolved\Document;
use Zol\Apifony\Resolved\MediaType;
use Zol\Apifony\Resolved\Resolver;
use Zol\Apifony\Resolved\Response;
use Zol\Apifony\Resolved\Schema;
use Zol\Apifony\Resolved\SchemaRef;

class Bundle implements File
{
    /**
     * @throws Exception
     * @throws OpenApiException
     */
    public static function build(
        string $rawName,
        string $packageName,
        string $namespace,
        OpenApi $openApi,
    ): self {
        $names = new NameRegistry();
        $names->claimBundle($name = Naming::forClass($rawName), Origin::spec('bundle name', $rawName, ['documentation root']));

        $document = Resolver::resolve($openApi);

        $bundle = new self(
            $name,
            $namespace,
            $formats = self::buildFormats($namespace, $name, $document, $names),
            $models = self::buildModels($namespace, $document->components, $names),
            $api = Api::build($namespace, $name, $document, $models, $names),
            RoutesConfig::build($namespace, $api, $names),
            ServicesConfig::build($namespace, $api, $formats, $names),
            new ComposerJson($packageName, $namespace),
            new ConstraintValidatorFactory($namespace),
        );

        // The net under everything the scopes above cannot see between them, and the reason it sits
        // here rather than in the command: a bundle built programmatically used to get no check at
        // all.
        $writtenPaths = [];
        foreach ($bundle->getFiles() as $file) {
            $path = $file->getFolder() === '' ? $file->getName() : "{$file->getFolder()}/{$file->getName()}";
            if (isset($writtenPaths[$path])) {
                throw new Exception(\sprintf('Two generated files would be written to \'%s\'.', $path), ['documentation root']);
            }
            $writtenPaths[$path] = true;
        }

        return $bundle;
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
    private static function buildFormats(string $namespace, string $name, Document $document, NameRegistry $names): array
    {
        $rawFormatNames = [];

        // A slot written as a reference is not followed. Nothing is lost, since the components
        // buckets are walked in full and every target is reached there, and not following one is
        // what keeps the walk from going round a recursive schema forever.
        $collect = static function (SchemaRef $ref) use (&$collect, &$rawFormatNames): void {
            if ($ref->isReference) {
                return;
            }

            $schema = $ref->getTarget();
            if ($schema->format !== null) {
                $rawFormatNames[$schema->format] = null;
            }
            foreach ($schema->properties as $property) {
                $collect($property);
            }
            if ($schema->items !== null) {
                $collect($schema->items);
            }
        };

        foreach (self::getSchemaSlots($document) as $slot) {
            $collect($slot);
        }

        $formats = [];
        foreach ($rawFormatNames as $rawFormatName => $_) {
            $formats[$rawFormatName] = Format::build($namespace, $name, $rawFormatName, $names);
        }

        return $formats;
    }

    /**
     * Every place the document declares a schema, in the order the buckets and then the paths are
     * written. That order is not incidental: it is the order the formats are met in, and therefore
     * the order their validators are declared in.
     *
     * @return list<SchemaRef>
     *
     * @throws Exception
     */
    private static function getSchemaSlots(Document $document): array
    {
        $slots = [];

        foreach ($document->components->schemas ?? [] as $schema) {
            $slots[] = SchemaRef::inline($schema);
        }
        foreach ($document->components->parameters ?? [] as $parameter) {
            $slots[] = self::requireSchema($parameter->schema, 'Parameter', $parameter->path);
        }
        foreach ($document->components->requestBodies ?? [] as $requestBody) {
            $slots = array_merge($slots, self::getContentSlots($requestBody->content));
        }
        foreach ($document->components->responses ?? [] as $response) {
            $slots = array_merge($slots, self::getResponseSlots($response));
        }
        foreach ($document->components->headers ?? [] as $header) {
            $slots[] = self::requireSchema($header->schema, 'Header', $header->path);
        }

        foreach ($document->paths->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->parameters as $parameter) {
                $slots[] = self::requireSchema($parameter->schema, 'Parameter', $parameter->path);
            }
            foreach ($pathItem->operations as $operation) {
                foreach ($operation->parameters as $parameter) {
                    $slots[] = self::requireSchema($parameter->schema, 'Parameter', $parameter->path);
                }
                if ($operation->requestBody !== null) {
                    $slots = array_merge($slots, self::getContentSlots($operation->requestBody->content));
                }
                foreach ($operation->responses->responses ?? [] as $response) {
                    $slots = array_merge($slots, self::getResponseSlots($response));
                }
            }
        }

        return $slots;
    }

    /**
     * @return list<SchemaRef>
     *
     * @throws Exception
     */
    private static function getResponseSlots(Response $response): array
    {
        $slots = [];
        foreach ($response->headers as $header) {
            $slots[] = self::requireSchema($header->schema, 'Header', $header->path);
        }

        return array_merge($slots, self::getContentSlots($response->content));
    }

    /**
     * @param array<string, MediaType> $content
     *
     * @return list<SchemaRef>
     *
     * @throws Exception
     */
    private static function getContentSlots(array $content): array
    {
        $slots = [];
        foreach ($content as $mediaType) {
            $slots[] = self::requireSchema($mediaType->schema, 'MediaType', $mediaType->path);
        }

        return $slots;
    }

    /**
     * @param list<string> $path
     *
     * @throws Exception
     */
    private static function requireSchema(?SchemaRef $ref, string $subject, array $path): SchemaRef
    {
        if ($ref === null) {
            throw new Exception(\sprintf('%s objects without schema are not supported.', $subject), $path);
        }

        return $ref;
    }

    /**
     * @return list<Model>
     *
     * @throws Exception
     */
    private static function buildModels(string $namespace, ?Components $components, NameRegistry $names): array
    {
        $collector = ModelCollector::forComponents($namespace, $names);

        foreach ($components->schemas ?? [] as $rawName => $schema) {
            $collector->collect($rawName, SchemaRef::inline($schema));
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
