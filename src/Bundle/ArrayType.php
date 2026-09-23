<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

class ArrayType implements Type
{
    private readonly Schema $schema;
    private readonly Type $itemType;
    private readonly ?string $usedModel;

    /**
     * @throws Exception
     */
    public function __construct(
        Reference|Schema $schema,
        private readonly bool $nullable,
        string $className,
        ?Components $components,
    ) {
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.', $schema->path);
            }
            $schema = $components->schemas[$schema->getName()];
        }

        $items = $schema->items;
        if ($items === null) {
            throw new Exception('Schema objects of array type without items attribute are not supported.', $schema->path);
        }
        $usedModel = null;
        if ($items instanceof Reference) {
            if ($components === null || !isset($components->schemas[$items->getName()])) {
                throw new Exception('Reference not found in schemas components.', $items->path);
            }
            $items = $components->schemas[$className = $usedModel = $items->getName()];
            $className = Naming::forClass($className);
        }

        $this->schema = $schema;
        $this->itemType = TypeFactory::build($className, $items, $components);
        $this->usedModel = $usedModel;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getDefaultExpr(): Expr
    {
        if (!$this->schema->hasDefault) {
            throw new \RuntimeException();
        }

        if ($this->schema->default === null) {
            return new ConstFetch(new Name('null'));
        }

        if (!\is_array($this->schema->default)) {
            throw new \RuntimeException();
        }

        return new Array_([], ['kind' => Array_::KIND_SHORT]);
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Array path parameters are not supported.', $this->schema->path);
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', [], enforcedByDenormalizer: true);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', Naming::forClass($this->schema->format)), [], $this->schema->format);
        }

        if ($this->schema->minItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['min' => $this->schema->minItems]);
        }

        if ($this->schema->maxItems !== null) {
            $constraints[] = new Constraint('Assert\Count', ['max' => $this->schema->maxItems]);
        }

        if ($this->schema->uniqueItems) {
            $constraints[] = new Constraint('Assert\Unique', []);
        }

        $itemConstraints = $this->itemType->getConstraints();

        if ($this->itemType instanceof ObjectType) {
            foreach ($itemConstraints as $index => $itemConstraint) {
                if ($itemConstraint->getName() === 'Assert\Valid') {
                    $constraints[] = new Constraint('Assert\Valid', []);
                    unset($itemConstraints[$index]);
                }
            }
        }

        // Symfony only enforces the item type of a collection from version 8, so it is asserted here
        // to hold on every version the generated bundle supports.
        $itemTypeConstraint = match ($this->itemType->getBuiltInPhpType()) {
            'string' => new Constraint('Assert\Type', ['type' => 'string'], enforcedByDenormalizer: true),
            'int' => new Constraint('Assert\Type', ['type' => 'int'], enforcedByDenormalizer: true),
            // A number accepts an int as well as a float, as the scalar readers do.
            'float' => new Constraint('Assert\Type', ['type' => ['int', 'float']], enforcedByDenormalizer: true),
            'bool' => new Constraint('Assert\Type', ['type' => 'bool'], enforcedByDenormalizer: true),
            'array' => new Constraint('Assert\Type', ['type' => 'array'], enforcedByDenormalizer: true),
            // An object item needs no assertion: the normalizer cannot build one from a scalar.
            // Its short class name would not resolve as an Assert\Type argument anyway.
            default => null,
        };
        if ($itemTypeConstraint !== null) {
            $itemConstraints = array_merge([$itemTypeConstraint], $itemConstraints);
        }

        if (\count($itemConstraints) > 0) {
            $constraints[] = new Constraint('Assert\All', ['constraints' => $itemConstraints]);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'array';
    }

    public function getInitValue(): Expr
    {
        return new Array_([], ['kind' => Array_::KIND_SHORT]);
    }

    public function getUsedModel(): ?string
    {
        return $this->usedModel;
    }

    public function getDocAst(): TypeNode
    {
        $type = new GenericTypeNode(new IdentifierTypeNode('list'), [$this->itemType->getDocAst()]);

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function hasInformativeDocType(): bool
    {
        return true;
    }

    /**
     * @return list<string>
     *
     * @throws Exception
     */
    public function getUsedModelNames(): array
    {
        if ($this->usedModel !== null) {
            return [$this->usedModel];
        }

        return $this->itemType->getUsedModelNames();
    }

    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        $key = $context->nextVariable();
        $item = $context->nextVariable();
        $itemPath = $context->nextVariable();
        $itemValue = $context->nextVariable();

        return $context->wrapNullable($this->nullable, $source, $target, fn (Expr $value): array => [
            new Expression(new Assign($target, new Array_([], ['kind' => Array_::KIND_SHORT]))),
            new Foreach_(
                $f->methodCall($f->var('this'), \sprintf('denormalizeList%s', $context->getSource()), [$value, $path]),
                $item,
                [
                    'keyVar' => $key,
                    'stmts' => array_merge(
                        [new Expression(new Assign($itemPath, new Encapsed([$path, new EncapsedStringPart('['), $key, new EncapsedStringPart(']')])))],
                        $this->itemType->getParameterDenormalizationStmts($item, $itemValue, $itemPath, $context),
                        [new Expression(new Assign(new ArrayDimFetch($target), $itemValue))],
                    ),
                ],
            ),
        ]);
    }

    public function asName(): Name
    {
        return new Name($this->nullable ? '?array' : 'array');
    }
}
