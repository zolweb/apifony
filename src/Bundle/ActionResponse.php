<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Response;
use Zol\Apifony\OpenApi\Schema;

use function Symfony\Component\String\u;

class ActionResponse implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        int $code,
        Response $response,
        Reference|Schema|null $payload,
        ?Components $components,
    ): self {
        $className = u(\sprintf('%s_%s_ResponsePayload', $actionName, $code))->camel()->title()->toString();

        $payloadModels = [];
        $payloadType = null;
        $usedModelName = null;
        if ($payload !== null) {
            $schema = $payload;
            $hasModel = true;
            if ($schema instanceof Reference) {
                if ($components === null || !isset($components->schemas[$schema->getName()])) {
                    throw new Exception('Reference not found in schemas components.', $schema->path);
                }
                $schema = $components->schemas[$className = $usedModelName = $schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);
            if (!$payloadType instanceof ObjectType) {
                throw new Exception('Only object schemas are supported for responses.', $schema->path);
            }

            if ($hasModel) {
                $addModels = static function (string $rawName, Reference|Schema $schema) use (&$addModels, &$payloadModels, $bundleNamespace, $aggregateName, $components): void {
                    if (!$schema instanceof Reference) {
                        $type = TypeFactory::build('', $schema, $components);
                        if ($type instanceof ObjectType) {
                            if (!($schema->extensions['x-raw'] ?? false)) {
                                $payloadModels[$rawName] = Model::build(
                                    $bundleNamespace,
                                    "{$bundleNamespace}\\Api\\{$aggregateName}",
                                    "src/Api/{$aggregateName}",
                                    $rawName,
                                    $schema,
                                    $components,
                                    false,
                                );
                                foreach ($schema->properties as $propertyName => $property) {
                                    $addModels("{$rawName}_{$propertyName}", $property);
                                }
                            }
                        } elseif ($type instanceof ArrayType) {
                            if ($schema->items === null) {
                                throw new Exception('Schema objects of array type without items attribute are not supported.', $schema->path);
                            }
                            $addModels($rawName, $schema->items);
                        }
                    }
                };

                $addModels($className, $schema);
            }
        }

        return new self(
            $bundleNamespace,
            $aggregateName,
            u(\sprintf('%s_%s_Response', $actionName, $code))->camel()->title()->toString(),
            $code,
            $payloadType,
            array_map(
                static fn (string $name) => ActionResponseHeader::build($name, $response->headers[$name], $components),
                array_keys($response->headers),
            ),
            $payloadModels,
            $usedModelName,
        );
    }

    /**
     * @param list<ActionResponseHeader> $headers
     * @param array<Model>               $payloadModels
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
        private readonly int $code,
        private readonly ?ObjectType $payloadType,
        private readonly array $headers,
        private readonly array $payloadModels,
        private readonly ?string $usedModelName,
    ) {
    }

    /**
     * @return array<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
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

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
        ;

        if ($this->payloadType !== null) {
            $constructor->addParam($f->param('payload')->setType($this->payloadType->asName())->makePublic()->makeReadonly());
        }

        foreach ($this->headers as $header) {
            $constructor->addParam($header->getParam());
        }

        if ($this->payloadType === null) {
            $constructor->addStmt(new Assign($f->propertyFetch($f->var('this'), 'payload'), $f->val('')));
        }

        $getHeadersMethod = $f->method('getHeaders')
            ->makePublic()
            ->setReturnType('array')
            ->setDocComment(<<<'COMMENT'
                /**
                 * @return array<string, ?string>
                 */
                COMMENT
            )
            ->addStmt(new Return_(new Array_(array_merge(
                array_map(static fn (ActionResponseHeader $header) => $header->getArrayItem(), $this->headers),
                [new ArrayItem($f->classConstFetch('self', 'CONTENT_TYPE'), $f->val('content-type'))],
            ))))
        ;

        $class = $f->class($this->name)
            ->addStmt($f->classConst('CODE', $this->code)->makePublic())
            ->addStmt($f->classConst('CONTENT_TYPE', $this->payloadType !== null ? 'application/json' : null)->makePublic())
            ->addStmt($constructor)
            ->addStmt($getHeadersMethod)
        ;

        if ($this->payloadType === null) {
            $class->addStmt($f->property('payload')->setType('string')->makePublic()->makeReadonly());
        }

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api\\{$this->aggregateName}")
            ->addStmts($this->usedModelName !== null ? [$f->use("{$this->bundleNamespace }\\Model\\{$this->usedModelName}")] : [])
            ->addStmt($class)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
