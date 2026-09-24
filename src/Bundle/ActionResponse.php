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
use Zol\Apifony\Resolved\Response;
use Zol\Apifony\Resolved\SchemaRef;

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
        ?SchemaRef $payload,
        NameRegistry $names,
    ): self {
        $className = Naming::forClass(\sprintf('%s_%s_ResponsePayload', $actionName, $code));

        $payloadModels = [];
        $payloadType = null;
        $usedModelName = null;
        $isReference = false;
        if ($payload !== null) {
            $isReference = $payload->isReference;
            $hasModel = !$isReference;
            if ($isReference) {
                $className = (string) $payload->getComponentName();
            }
            $schema = $payload->getTarget();
            $className = Naming::forClass($className);
            $payloadType = TypeFactory::build($className, $schema);
            if (!$payloadType instanceof ObjectType && !$payloadType instanceof RawType) {
                throw new Exception('Only object and raw schemas are supported for responses.', $schema->path);
            }
            $usedModelName = $isReference && ModelCollector::producesModel($payloadType) ? $className : null;

            if ($hasModel) {
                $collector = ModelCollector::forAggregate($bundleNamespace, $aggregateName, $names);
                $collector->collect($className, $payload);
                $payloadModels = $collector->getModels();
            }
        }

        return new self(
            $bundleNamespace,
            $aggregateName,
            Naming::forClass(\sprintf('%s_%s_Response', $actionName, $code)),
            $code,
            $payloadType,
            array_map(
                static fn (string $name) => ActionResponseHeader::build($name, $response->headers[$name]),
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
        private readonly ObjectType|RawType|null $payloadType,
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

        $contentType = $this->payloadType !== null ? 'application/json' : null;

        $getCodeMethod = $f->method('getCode')
            ->makePublic()
            ->setReturnType('int')
            ->addStmt(new Return_($f->val($this->code)))
        ;

        $getContentTypeMethod = $f->method('getContentType')
            ->makePublic()
            ->setReturnType('?string')
            ->addStmt(new Return_($f->val($contentType)))
        ;

        $getHeadersMethod = $f->method('getHeaders')
            ->makePublic()
            ->setReturnType('array')
            ->setDocComment(
                <<<'COMMENT'
                    /**
                     * @return array<string, ?string>
                     */
                    COMMENT
            )
            ->addStmt(new Return_(new Array_(array_merge(
                array_map(static fn (ActionResponseHeader $header) => $header->getArrayItem(), $this->headers),
                [new ArrayItem($f->val($contentType), $f->val('content-type'))],
            ), ['kind' => Array_::KIND_SHORT])))
        ;

        $class = $f->class($this->name)
            ->addStmt($constructor)
            ->addStmt($getCodeMethod)
            ->addStmt($getContentTypeMethod)
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
