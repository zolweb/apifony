<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Response;
use Zol\Ogen\OpenApi\Schema;

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
        ?string $contentType,
        Reference|Schema|null $payload,
        ?Components $components,
    ): self {
        if (!\in_array($contentType, [null, 'application/json'], true)) {
            throw new Exception('Responses with content type other thant \'application/json\' are not supported.');
        }

        $className = u(sprintf('%s_%s_%s_ResponsePayload', $actionName, $code, $contentType ?? 'empty'))->camel()->title()->toString();

        $payloadModels = [];
        $payloadType = null;
        $usedModelName = null;
        if ($payload !== null) {
            $schema = $payload;
            $hasModel = true;
            if ($schema instanceof Reference) {
                if ($components === null || !isset($components->schemas[$schema->getName()])) {
                    throw new Exception('Reference not found in schemas components.');
                }
                $schema = $components->schemas[$className = $usedModelName = $schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);
            if ($payloadType instanceof ArrayType) {
                throw new Exception('Responses with array schema are not supported.');
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
                                throw new Exception('Schema objects of array type without items attribute are not supported.');
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
            u(sprintf('%s_%s_%s_Response', $actionName, $code, $contentType ?? 'Empty'))->camel()->title()->toString(),
            $code,
            $contentType,
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
     * @param array<ActionResponseHeader> $headers
     * @param array<Model>                $payloadModels
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly string $name,
        private readonly int $code,
        private readonly ?string $contentType,
        private readonly ?Type $payloadType,
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

        if ($this->contentType !== null) {
            if ($this->payloadType === null) { // todo code smell
                throw new \RuntimeException();
            }
            $constructor->addParam($f->param('payload')->setType($this->payloadType->asName())->makePublic()->makeReadonly());
        }

        foreach ($this->headers as $header) {
            $constructor->addParam($header->getParam());
        }

        if ($this->contentType === null) {
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
            ->addStmt($f->classConst('CONTENT_TYPE', $this->contentType)->makePublic())
            ->addStmt($constructor)
            ->addStmt($getHeadersMethod)
        ;

        if ($this->contentType === null) {
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
