<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TryCatch;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\MediaType;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

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
            throw new Exception('Mediatypes without schema are not supported.');
        }
        $schema = $mediaType->schema;
        $hasModel = true;
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.');
            }
            $schema = $components->schemas[$className = $usedModelName = $schema->getName()];
            $hasModel = false;
        }
        $payloadType = TypeFactory::build($className, $schema, $components);
        if (!$payloadType instanceof ObjectType) {
            throw new Exception('Only object schema are supported for request bodies.');
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

        return new self(
            $payloadType,
            array_values($payloadModels),
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
     * @return list<Stmt>
     */
    public function getStmts(): array
    {
        $f = new BuilderFactory();

        return [
            new Expression(new Assign($f->var('requestBodyPayload'), $f->methodCall($f->new('\ReflectionClass', [$f->classConstFetch($this->getPayloadTypeName(), 'class')]), 'newInstanceWithoutConstructor'))),
            new TryCatch([
                new Expression(new Assign($f->var('requestBodyPayload'), $f->methodCall($f->var('this'), 'getObjectRequestBody', array_merge([$f->var('request')], $this->getPayloadBuiltInPhpType() === 'object' ? [$f->classConstFetch($this->getPayloadTypeName(), 'class')] : [])))),
                new Expression($f->methodCall($f->var('this'), 'validateRequestBody', [
                    $f->var('requestBodyPayload'),
                    array_map(
                        static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                        $this->payloadType->getConstraints(),
                    ),
                ])),
            ], [
                new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->val('requestBody')), new Array_([new ArrayItem($f->methodCall($f->var('e'), 'getMessage'))]))),
                ]),
                new Catch_([new Name('RequestBodyValidationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var('errors'), $f->val('requestBody')), $f->propertyFetch($f->var('e'), 'messages'))),
                ]),
            ]),
        ];
    }
}
