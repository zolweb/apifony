<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Printer\Printer;
use Zol\Apifony\Resolved\Parameter;
use Zol\Apifony\Resolved\Schema;

class ActionParameter
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Parameter $parameter,
        int $ordinal,
    ): self {
        if ($parameter->schema === null) {
            throw new Exception('Parameter objects without schema attribute are not supported.', $parameter->path);
        }

        if ($parameter->in === 'path') {
            // The router injects a path parameter into the controller by name, so the raw name is
            // used as the PHP parameter and cannot be normalized away.
            Naming::assertIdentifier($parameter->name, \sprintf('Path parameter \'%s\'', $parameter->name), $parameter->path);
        }
        $variableName = \sprintf('%s%s', $parameter->in[0], Naming::forClass($parameter->name));
        $className = "{$actionClassName}_{$parameter->name}";

        $ref = $parameter->schema;
        $isReference = $ref->isReference;
        if ($isReference) {
            $className = (string) $ref->getComponentName();
        }
        $schema = $ref->getTarget();
        $className = Naming::forClass($className);

        if ($parameter->required && $schema->hasDefault) {
            throw new Exception('Every required parameter must not have a default value.', $parameter->path);
        }
        if (!$parameter->required && !$schema->hasDefault) {
            throw new Exception('Every non required parameter must have a default value.', $parameter->path);
        }

        $type = TypeFactory::build($className, $schema);

        // A reference is rendered as the component class only when the referenced schema is one
        // ModelCollector emits; an array or a scalar component is inlined and names no class of
        // its own, so what has to be imported is then whatever the type itself references.
        $usedModelNames = $isReference && ModelCollector::producesModel($type) ? [$className] : $type->getUsedModelNames();

        $models = [];
        if ($type instanceof ArrayType || $type instanceof ObjectType || $type instanceof RawType) {
            if ($parameter->in === 'path') {
                // Delegates to the type so that the existing, more precise message is kept.
                $type->getRouteRequirementPattern();
            }
            if ($parameter->in !== 'query') {
                throw new Exception('Array, object and raw parameters are only supported in query.', $parameter->path);
            }
            if (!$isReference) {
                // A referenced schema is already emitted among the components models.
                $collector = ModelCollector::forAggregate($bundleNamespace, $aggregateName);
                $collector->collect($className, $ref);
                $models = $collector->getModels();
            }
        }

        return new self(
            $variableName,
            $parameter,
            $type,
            $schema,
            $ordinal,
            $models,
            array_values(array_unique($usedModelNames)),
        );
    }

    /**
     * @param list<Model>  $models
     * @param list<string> $usedModelNames
     */
    private function __construct(
        private readonly string $variableName,
        private readonly Parameter $parameter,
        private readonly Type $type,
        private readonly Schema $schema,
        private readonly int $ordinal,
        private readonly array $models,
        private readonly array $usedModelNames,
    ) {
    }

    public function getIn(): string
    {
        return $this->parameter->in;
    }

    public function getRawName(): string
    {
        return $this->parameter->name;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * The models inlined in this parameter schema, which the bundle must emit.
     *
     * @return list<Model>
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * The components schemas this parameter references, which the generated files must import.
     *
     * @return list<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
    }

    /**
     * @return list<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function getRouteRequirementPattern(): string
    {
        return $this->type->getRouteRequirementPattern();
    }

    public function asVariable(): Variable
    {
        return new Variable($this->variableName);
    }

    public function asParam(bool $rawName = false): Param
    {
        return new Param(
            var: new Variable($rawName ? $this->parameter->name : $this->variableName),
            type: $this->type->asName(),
        );
    }

    /**
     * The "param" tag documenting this parameter, or null when the native type hint already says
     * everything there is to say.
     */
    public function getDocTagNode(): ?PhpDocTagNode
    {
        if (!$this->type->hasInformativeDocType()) {
            return null;
        }

        return new PhpDocTagNode('@param', new ParamTagValueNode($this->type->getDocAst(), false, "\${$this->variableName}", '', false));
    }

    public function shouldBePositionedBefore(self $other): bool
    {
        if ($this->schema->hasDefault !== $other->schema->hasDefault) {
            return $other->schema->hasDefault;
        }

        return $this->ordinal < $other->ordinal;
    }

    /**
     * Appends one entry to the flat list of errors the action returns.
     */
    private function getAppendErrorStmt(Expr $path, Expr $code, Expr $message): Stmt
    {
        $f = new BuilderFactory();

        return new Expression(new Assign(new ArrayDimFetch($f->var('errors')), new Array_([
            new ArrayItem($f->val($this->parameter->in), $f->val('in')),
            new ArrayItem($path, $f->val('path')),
            new ArrayItem($code, $f->val('code')),
            new ArrayItem($message, $f->val('message')),
        ], ['kind' => Array_::KIND_SHORT])));
    }

    /**
     * @return list<Catch_>
     */
    private function getCatches(bool $denormalization): array
    {
        $f = new BuilderFactory();

        $catches = [];
        if ($denormalization) {
            $catches[] = new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                $this->getAppendErrorStmt(
                    $f->propertyFetch($f->var('e'), 'path'),
                    $f->propertyFetch($f->var('e'), 'errorCode'),
                    $f->methodCall($f->var('e'), 'getMessage'),
                ),
            ]);
        }

        if (\count($this->getResidualConstraints()) === 0) {
            return $catches;
        }

        $catches[] = new Catch_([new Name('ValidationException')], $f->var('e'), [
            new Foreach_($f->propertyFetch($f->var('e'), 'errors'), $f->var('error'), ['stmts' => [
                new Expression(new Assign(new ArrayDimFetch($f->var('errors')), new Array_([
                    new ArrayItem($f->val($this->parameter->in), $f->val('in')),
                    new ArrayItem($f->var('error'), null, false, [], true),
                ], ['kind' => Array_::KIND_SHORT]))),
            ]]),
        ]);

        return $catches;
    }

    /**
     * @return Stmt[]
     */
    public function getPathSanitizationStmts(): array
    {
        $f = new BuilderFactory();

        $stmts = [new Expression(new Assign(new Variable($this->variableName), new Variable($this->parameter->name)))];

        $validateStmts = $this->getValidateStmts();
        if (\count($validateStmts) > 0) {
            $stmts[] = new TryCatch($validateStmts, $this->getCatches(false));
        }

        return $stmts;
    }

    /**
     * @return Stmt[]
     */
    public function getNonPathSanitizationStmts(): array
    {
        $f = new BuilderFactory();

        return [
            new Expression(new Assign(new Variable($this->variableName), $this->type->getInitValue())),
            new TryCatch(array_merge(
                [new Expression(new Assign(new Variable($this->variableName), $this->getReadExpr()))],
                $this->getValidateStmts(),
            ), $this->getCatches(true)),
        ];
    }

    /**
     * The controller method denormalizing this parameter, for the parameters whose type is too deep
     * to be read by one of the AbstractController readers. Null for scalar parameters.
     *
     * @throws Exception
     */
    public function getDenormalizerMethod(DenormalizationContext $context): ?ClassMethod
    {
        if (!$this->isComplex()) {
            return null;
        }

        $f = new BuilderFactory();
        $context->resetVariables();
        $result = $context->nextVariable();

        $returnDoc = $this->type->hasInformativeDocType()
            ? \sprintf(" * @return %s\n *\n", (new Printer())->print($this->type->getDocAst()))
            : '';

        $method = $f->method($this->getDenormalizerMethodName())
            ->makePrivate()
            ->addParam($f->param('request')->setType('Request'))
            ->addParam($f->param('name')->setType('string'))
            ->addParam($f->param('in')->setType('string'))
            ->setReturnType($this->type->asName())
            ->setDocComment("/**\n{$returnDoc} * @throws DenormalizationException\n */")
            ->addStmt(new If_(new BooleanNot($f->methodCall($f->var('this'), 'hasParameter', [$f->var('request'), $f->var('name'), $f->var('in')])), ['stmts' => [
                $this->parameter->required
                    ? new Expression(new Throw_($f->new('DenormalizationException', [$f->var('name'), $f->val('required'), $f->val('This value is required.')])))
                    : new Return_($this->type->getDefaultExpr()),
            ]]))
        ;

        $stmts = $this->type->getParameterDenormalizationStmts(
            $f->methodCall($f->var('this'), 'getRawParameter', [$f->var('request'), $f->var('name'), $f->var('in')]),
            $result,
            $f->var('name'),
            $context,
        );

        // Returning the last assignment directly, as php-cs-fixer would rewrite it that way anyway.
        $returnExpr = $result;
        $lastStmt = end($stmts);
        if ($lastStmt instanceof Expression && $lastStmt->expr instanceof Assign && $lastStmt->expr->var === $result) {
            array_pop($stmts);
            $returnExpr = $lastStmt->expr->expr;
        }

        foreach ($stmts as $stmt) {
            $method->addStmt($stmt);
        }

        return $method->addStmt(new Return_($returnExpr))->getNode();
    }

    /**
     * The models this parameter is denormalized into, which the AbstractController emits a method
     * for. A scalar parameter goes through the fixed readers and needs none.
     *
     * @return list<ObjectType>
     *
     * @throws Exception
     */
    public function getDenormalizationRootModels(): array
    {
        return $this->type->getDenormalizationRootModels();
    }

    private function isComplex(): bool
    {
        return $this->type instanceof ArrayType || $this->type instanceof ObjectType || $this->type instanceof RawType;
    }

    private function getDenormalizerMethodName(): string
    {
        return \sprintf('denormalize%sParameter', ucfirst($this->variableName));
    }

    private function getReadExpr(): Expr
    {
        $f = new BuilderFactory();

        if ($this->isComplex()) {
            return $f->methodCall($f->var('this'), $this->getDenormalizerMethodName(), [
                $f->var('request'),
                new String_($this->parameter->name),
                $this->parameter->in,
            ]);
        }

        return $f->methodCall(
            $f->var('this'),
            \sprintf('get%s%sParameter', ucfirst($this->type->getBuiltInPhpType()), $this->type->isNullable() ? 'OrNull' : ''),
            array_merge(
                [$f->var('request'), new String_($this->parameter->name), $this->parameter->in, $this->parameter->required],
                $this->schema->hasDefault ? [$this->type->getDefaultExpr()] : [],
            ),
        );
    }

    /**
     * What is left to check once the value has been read.
     *
     * A complex parameter is rebuilt by a generated denormalizer, which already guarantees types,
     * non nullability, enum membership and integer bounds at every depth. A scalar one goes through
     * the older readers, which coerce the type but know nothing of enums or bounds, so only PHP's
     * own guarantee applies there.
     *
     * @return list<Constraint>
     */
    public function getResidualConstraints(): array
    {
        return $this->isComplex()
            ? Constraint::filterResidual($this->getConstraints())
            : Constraint::filterPhpGuaranteed($this->getConstraints());
    }

    private function getConstraintsAst(): Array_
    {
        return new Array_(array_map(
            static fn (Constraint $constraint): ArrayItem => new ArrayItem($constraint->getInstantiationAst()),
            $this->getResidualConstraints(),
        ), ['kind' => Array_::KIND_SHORT]);
    }

    /**
     * @return list<Stmt>
     */
    private function getValidateStmts(): array
    {
        $f = new BuilderFactory();

        if (\count($this->getResidualConstraints()) === 0) {
            return [];
        }

        return [new Expression($f->methodCall($f->var('this'), 'validate', [
            new Variable($this->variableName),
            new String_($this->parameter->name),
            $this->getConstraintsAst(),
        ]))];
    }
}
