<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

use function Symfony\Component\String\u;

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
            $className = u($className)->camel()->title()->toString();
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
        return new ConstFetch(new Name('null'));
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Array path parameters are not supported.', $this->schema->path);
    }

    public function getRequestBodyPayloadTypeCheckingAst(): Expr
    {
        $f = new BuilderFactory();

        return new Expr\BinaryOp\BooleanAnd($f->funcCall('is_array', [$f->var('requestBodyPayload')]), $this->itemType->getRequestBodyPayloadTypeCheckingAst());
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
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
        return new Expr\Array_();
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

    public function asName(): Name
    {
        return new Name($this->nullable ? '?array' : 'array');
    }
}
