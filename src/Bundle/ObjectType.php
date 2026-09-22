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
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Schema;

use function Symfony\Component\String\u;

class ObjectType implements Type
{
    private readonly bool $isRaw;

    /**
     * @var list<ModelAttribute>|null
     */
    private ?array $attributes = null;

    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
        private readonly string $name,
        private readonly ?Components $components = null,
    ) {
        $this->isRaw = ($this->schema->extensions['x-apifony-raw'] ?? false) === true;
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
        $constraints = [];

        if (!$this->isRaw) {
            $constraints[] = new Constraint('Assert\Valid', []);
        }

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(\sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        return $constraints;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBuiltInPhpType(): string
    {
        return $this->isRaw ? 'mixed' : 'object';
    }

    public function getInitValue(): Expr
    {
        $f = new BuilderFactory();

        if ($this->isRaw) {
            return new ConstFetch(new Name('null'));
        }

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
     * The attributes of the model this type is rendered as. Built lazily: building them eagerly in
     * the constructor would make a recursive schema loop forever.
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
                    $this->components,
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
        if ($this->isRaw) {
            return [];
        }

        $names = [];
        foreach ($this->getAttributes() as $attribute) {
            $usedModelName = $attribute->getUsedModelName();
            if ($usedModelName !== null) {
                $names[] = $usedModelName;

                continue;
            }
            foreach ($attribute->getType()->getUsedModelNames() as $name) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public function getDocAst(): TypeNode
    {
        $type = $this->isRaw
            ? new IdentifierTypeNode('mixed')
            : new IdentifierTypeNode($this->name);

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function hasInformativeDocType(): bool
    {
        return false;
    }

    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        if ($this->isRaw) {
            return $context->wrapNullable($this->nullable, $source, $target, static fn (Expr $value): array => [
                new Expression(new Assign($target, $f->methodCall($f->var('this'), \sprintf('denormalizeMap%s', $context->getSource()), array_merge([$value, $path], $context->getLocationArgs())))),
            ]);
        }

        $method = $context->registerModel($this);

        return $context->wrapNullable($this->nullable, $source, $target, static fn (Expr $value): array => [
            new Expression(new Assign($target, $f->methodCall($f->var('this'), $method, array_merge([$value, $path], $context->getLocationArgs())))),
        ]);
    }

    /**
     * The path of one of this model properties, as the generated code builds it: query parameters
     * use the bracket notation they were sent with, a request body uses dots.
     */
    private function getPropertyPathExpr(Expr $path, string $rawName, DenormalizationContext $context): Expr
    {
        $f = new BuilderFactory();

        if ($context->getSource() === DenormalizationContext::SOURCE_QUERY) {
            return new Encapsed([$path, new EncapsedStringPart("[{$rawName}]")]);
        }

        return $f->methodCall($f->var('this'), 'appendJsonPath', [$path, $f->val($rawName)]);
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
        $isQuery = $context->getSource() === DenormalizationContext::SOURCE_QUERY;

        $method = $f->method(DenormalizationContext::getModelMethodName($this->name, $context->getSource()))
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->addParam($f->param('path')->setType('string'))
            ->setReturnType($this->name)
            ->setDocComment("/**\n * @throws DenormalizationException\n */")
        ;
        if ($isQuery) {
            $method->addParam($f->param('in')->setType('string'));
        }

        $values = $context->nextVariable();
        $method->addStmt(new Expression(new Assign($values, $f->methodCall($f->var('this'), \sprintf('denormalizeMap%s', $context->getSource()), array_merge([$f->var('value'), $f->var('path')], $context->getLocationArgs())))));

        $args = [];
        foreach ($this->getAttributes() as $attribute) {
            $rawName = $attribute->getRawName();
            $propertyPath = $context->nextVariable();
            $propertyValue = $context->nextVariable();
            $propertyPathStmt = new Expression(new Assign($propertyPath, $this->getPropertyPathExpr($f->var('path'), $rawName, $context)));

            if (\in_array($rawName, $this->schema->required, true)) {
                $method->addStmt($propertyPathStmt);
                foreach ($attribute->getType()->getParameterDenormalizationStmts(
                    $f->methodCall($f->var('this'), \sprintf('getRequired%sProperty', $context->getSource()), array_merge([$values, $f->val($rawName), $propertyPath], $context->getLocationArgs())),
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
        return new Name(($this->nullable ? '?' : '').($this->isRaw ? 'mixed' : $this->name));
    }
}
