<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TryCatch;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\MediaType;
use Zol\Apifony\OpenApi\Reference;

use function Symfony\Component\String\u;

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
        ?Components $components,
    ): self {
        $className = u(\sprintf('%s_RequestBodyPayload', $actionName))->camel()->title()->toString();

        $payloadModels = [];
        $usedModelName = null;
        if ($mediaType->schema === null) {
            throw new Exception('Mediatypes without schema are not supported.', $mediaType->path);
        }
        $schema = $mediaType->schema;
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
            throw new Exception('Only object schema are supported for request bodies.', $schema->path);
        }

        if ($hasModel) {
            $collector = ModelCollector::forAggregate($bundleNamespace, $aggregateName, $components);
            $collector->collect($className, $schema);
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
        private readonly ObjectType $payloadType,
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
        if ($this->getPayloadBuiltInPhpType() === 'object') {
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

        if ($this->getPayloadBuiltInPhpType() !== 'object') {
            return $json;
        }

        return $f->methodCall(
            $f->var('this'),
            DenormalizationContext::getModelMethodName($this->getPayloadTypeName()->toString(), DenormalizationContext::SOURCE_JSON),
            [$json, $f->val('')],
        );
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
                new Expression($f->methodCall($f->var('this'), 'validateRequestBody', [
                    $f->var('requestBodyPayload'),
                    new Array_(array_map(
                        static fn (Constraint $constraint): ArrayItem => new ArrayItem($constraint->getInstantiationAst()),
                        $this->payloadType->getConstraints(),
                    ), ['kind' => Array_::KIND_SHORT]),
                ])),
            ], [
                new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                    new Expression(new Assign($f->var('requestBodyErrors'), new Array_([new ArrayItem($f->methodCall($f->var('e'), 'getMessage'))], ['kind' => Array_::KIND_SHORT]))),
                ]),
                new Catch_([new Name('RequestBodyValidationException')], $f->var('e'), [
                    new Expression(new Assign($f->var('requestBodyErrors'), $f->propertyFetch($f->var('e'), 'messages'))),
                ]),
            ]),
        ];
    }
}
