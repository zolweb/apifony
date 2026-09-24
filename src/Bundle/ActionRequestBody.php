<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\TryCatch;
use Zol\Apifony\Resolved\MediaType;

class ActionRequestBody
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        MediaType $mediaType,
    ): self {
        $className = Naming::forClass(\sprintf('%s_RequestBodyPayload', $actionName));

        $payloadModels = [];
        if ($mediaType->schema === null) {
            throw new Exception('Mediatypes without schema are not supported.', $mediaType->path);
        }
        $ref = $mediaType->schema;
        $isReference = $ref->isReference;
        $hasModel = !$isReference;
        if ($isReference) {
            $className = (string) $ref->getComponentName();
        }
        $schema = $ref->getTarget();
        $className = Naming::forClass($className);
        $payloadType = TypeFactory::build($className, $schema);
        if (!$payloadType instanceof ObjectType && !$payloadType instanceof RawType) {
            throw new Exception('Only object and raw schemas are supported for request bodies.', $schema->path);
        }
        $usedModelName = $isReference && ModelCollector::producesModel($payloadType) ? $className : null;

        if ($hasModel) {
            $collector = ModelCollector::forAggregate($bundleNamespace, $aggregateName);
            $collector->collect($className, $ref);
            $payloadModels = $collector->getModels();
        }

        return new self(
            $payloadType,
            $payloadModels,
            $usedModelName,
        );
    }

    /**
     * @param list<Model> $payloadModels
     */
    private function __construct(
        private readonly ObjectType|RawType $payloadType,
        private readonly array $payloadModels,
        private readonly ?string $usedModelName,
    ) {
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
    }

    public function getPayloadTypeName(): Name
    {
        return $this->payloadType->asName();
    }

    public function getPayloadBuiltInPhpType(): string
    {
        return $this->payloadType->getBuiltInPhpType();
    }

    /**
     * @return list<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }

    /**
     * Populates the registry with the model this request body denormalizes.
     *
     * @throws Exception
     */
    public function registerDenormalizationModels(DenormalizationContext $context): void
    {
        if ($this->payloadType instanceof ObjectType) {
            $context->registerModel($this->payloadType);
        }
    }

    /**
     * A raw payload is whatever json_decode returned, handed over untouched. Anything else is
     * rebuilt by the denormalizer generated for its model.
     */
    private function getReadExpr(BuilderFactory $f): Expr
    {
        $json = $f->methodCall($f->var('this'), 'getJsonRequestBody', [$f->var('request')]);

        if (!$this->payloadType instanceof ObjectType) {
            return $json;
        }

        return $f->methodCall(
            $f->var('this'),
            DenormalizationContext::getModelMethodName($this->getPayloadTypeName()->toString(), DenormalizationContext::SOURCE_JSON),
            [$json, $f->val('')],
        );
    }

    /**
     * @return list<Constraint>
     */
    public function getResidualConstraints(): array
    {
        return Constraint::filterResidual($this->payloadType->getConstraints());
    }

    /**
     * @return list<Stmt>
     */
    public function getStmts(): array
    {
        $f = new BuilderFactory();

        return [
            new Expression(new Assign($f->var('requestBodyPayload'), $this->payloadType->getInitValue())),
            new TryCatch([
                new Expression(new Assign($f->var('requestBodyPayload'), $this->getReadExpr($f))),
                new Expression($f->methodCall($f->var('this'), 'validate', [
                    $f->var('requestBodyPayload'),
                    $f->val(''),
                    new Array_(array_map(
                        static fn (Constraint $constraint): ArrayItem => new ArrayItem($constraint->getInstantiationAst()),
                        $this->getResidualConstraints(),
                    ), ['kind' => Array_::KIND_SHORT]),
                ])),
            ], [
                new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var('errors')), new Array_([
                        new ArrayItem($f->val('requestBody'), $f->val('in')),
                        new ArrayItem($f->propertyFetch($f->var('e'), 'path'), $f->val('path')),
                        new ArrayItem($f->propertyFetch($f->var('e'), 'errorCode'), $f->val('code')),
                        new ArrayItem($f->methodCall($f->var('e'), 'getMessage'), $f->val('message')),
                    ], ['kind' => Array_::KIND_SHORT]))),
                ]),
                new Catch_([new Name('ValidationException')], $f->var('e'), [
                    new Foreach_($f->propertyFetch($f->var('e'), 'errors'), $f->var('error'), ['stmts' => [
                        new Expression(new Assign(new ArrayDimFetch($f->var('errors')), new Array_([
                            new ArrayItem($f->val('requestBody'), $f->val('in')),
                            new ArrayItem($f->var('error'), null, false, [], true),
                        ], ['kind' => Array_::KIND_SHORT]))),
                    ]]),
                ]),
            ]),
        ];
    }
}
