<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\Resolved\Schema;

class ObjectType implements Type
{
    /**
     * @var list<ModelAttribute>|null
     */
    private ?array $attributes = null;

    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
        private readonly string $name,
    ) {
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getDefaultExpr(): Expr
    {
        if ($this->schema->hasDefault && $this->schema->default === null) {
            return new ConstFetch(new Name('null'));
        }

        throw new \RuntimeException();
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Object path parameters are not supported.', $this->schema->path);
    }

    public function getConstraints(): array
    {
        $constraints = [new Constraint('Assert\Valid', [])];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', [], enforcedByDenormalizer: true);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', Naming::forClass($this->schema->format)), [], $this->schema->format);
        }

        return $constraints;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Where the schema this type was built from sits in the specification. Two types carrying the
     * same name but a different path are two different models colliding.
     *
     * @return list<string>
     */
    public function getSchemaPath(): array
    {
        return $this->schema->path;
    }

    public function getBuiltInPhpType(): string
    {
        return 'object';
    }

    public function getInitValue(): Expr
    {
        $f = new BuilderFactory();

        return $f->methodCall(
            $f->new('\ReflectionClass', [$f->classConstFetch($this->name, 'class')]),
            'newInstanceWithoutConstructor',
        );
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    /**
     * The attributes of the model this type is rendered as. Built lazily: the resolved schema graph
     * is cyclic wherever the specification is, so building them in the constructor would loop
     * forever.
     *
     * @return list<ModelAttribute>
     *
     * @throws Exception
     */
    public function getAttributes(): array
    {
        if ($this->attributes === null) {
            $attributes = [];
            foreach ($this->schema->properties as $rawName => $property) {
                $attributes[] = ModelAttribute::build(
                    $this->name,
                    (string) $rawName,
                    $property,
                    \in_array((string) $rawName, $this->schema->required, true),
                );
            }
            $this->attributes = $attributes;
        }

        return $this->attributes;
    }

    /**
     * @return list<string>
     *
     * @throws Exception
     */
    public function getUsedModelNames(): array
    {
        $names = [];
        foreach ($this->getAttributes() as $attribute) {
            foreach ($attribute->getUsedModelNames() as $name) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public function getDocAst(): TypeNode
    {
        $type = new IdentifierTypeNode($this->name);

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function hasInformativeDocType(): bool
    {
        return false;
    }

    public function getDenormalizationRootModels(): array
    {
        return [$this];
    }

    /**
     * The models this one's own attributes are denormalized into, in the order the attributes are
     * emitted, which is the order the denormalizer method reaches them in.
     *
     * @return list<ObjectType>
     *
     * @throws Exception
     */
    public function getDenormalizationChildModels(): array
    {
        $models = [];
        foreach ($this->getAttributes() as $attribute) {
            foreach ($attribute->getType()->getDenormalizationRootModels() as $model) {
                $models[] = $model;
            }
        }

        return $models;
    }

    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        $method = DenormalizationContext::getModelMethodName($this->name, $context->getSource());

        return $context->wrapNullable($this->nullable, $source, $target, static fn (Expr $value): array => [
            new Expression(new Assign($target, $f->methodCall($f->var('this'), $method, [$value, $path]))),
        ]);
    }

    /**
     * The method denormalizing one value of this model. It is emitted once per model and per
     * source, on the AbstractController every controller extends, which is what makes a recursive
     * schema generate recursive code rather than an infinitely inlined type tree.
     *
     * @throws Exception
     */
    public function getModelDenormalizerMethod(DenormalizationContext $context): ClassMethod
    {
        $f = new BuilderFactory();
        $context->resetVariables();

        $method = $f->method(DenormalizationContext::getModelMethodName($this->name, $context->getSource()))
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('path')->setType('string'))
            ->setReturnType($this->name)
            ->setDocComment("/**\n * @throws DenormalizationException\n */")
        ;
        $values = $context->nextVariable();
        $method->addStmt(new Expression(new Assign($values, $f->methodCall($f->var('this'), \sprintf('denormalizeMap%s', $context->getSource()), [$f->var('value'), $f->var('path')]))));

        $args = [];
        foreach ($this->getAttributes() as $attribute) {
            $rawName = $attribute->getRawName();
            $propertyPath = $context->nextVariable();
            $propertyValue = $context->nextVariable();
            $propertyPathStmt = new Expression(new Assign($propertyPath, $f->methodCall($f->var('this'), 'appendPath', [$f->var('path'), $f->val($rawName)])));

            if (\in_array($rawName, $this->schema->required, true)) {
                $method->addStmt($propertyPathStmt);
                foreach ($attribute->getType()->getParameterDenormalizationStmts(
                    $f->methodCall($f->var('this'), \sprintf('getRequired%sProperty', $context->getSource()), [$values, $f->val($rawName), $propertyPath]),
                    $propertyValue,
                    $propertyPath,
                    $context,
                ) as $stmt) {
                    $method->addStmt($stmt);
                }
            } else {
                $method->addStmt(new Expression(new Assign($propertyValue, $attribute->getType()->getDefaultExpr())));
                $method->addStmt(new If_($f->funcCall('\array_key_exists', [$f->val($rawName), $values]), ['stmts' => array_merge(
                    [$propertyPathStmt],
                    $attribute->getType()->getParameterDenormalizationStmts(
                        new ArrayDimFetch($values, $f->val($rawName)),
                        $propertyValue,
                        $propertyPath,
                        $context,
                    ),
                )]));
            }

            $args[] = new Arg($propertyValue, name: new Identifier($rawName));
        }

        return $method->addStmt(new Return_($f->new($this->name, $args)))->getNode();
    }

    public function asName(): Name
    {
        return new Name(($this->nullable ? '?' : '').$this->name);
    }
}
