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
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Printer\Printer;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Parameter;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

class ActionParameter
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionClassName,
        Reference|Parameter $parameter,
        ?Components $components,
        int $ordinal,
    ): self {
        if ($parameter instanceof Reference) {
            if ($components === null || !isset($components->parameters[$parameter->getName()])) {
                throw new Exception('Reference not found in parameters components.', $parameter->path);
            }
            $parameter = $components->parameters[$parameter->getName()];
        }
        if ($parameter->schema === null) {
            throw new Exception('Parameter objects without schema attribute are not supported.', $parameter->path);
        }

        $variableName = \sprintf('%s%s', $parameter->in[0], Naming::forClass($parameter->name));
        $className = "{$actionClassName}_{$parameter->name}";

        $schema = $parameter->schema;
        $usedModelNames = [];
        $isReference = false;
        if ($schema instanceof Reference) {
            if ($components === null || !isset($components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.', $schema->path);
            }
            $isReference = true;
            $schema = $components->schemas[$className = $usedModelNames[] = $schema->getName()];
        }
        $className = Naming::forClass($className);

        if ($parameter->required && $schema->hasDefault) {
            throw new Exception('Every required parameter must not have a default value.', $parameter->path);
        }
        if (!$parameter->required && !$schema->hasDefault) {
            throw new Exception('Every non required parameter must have a default value.', $parameter->path);
        }

        $type = TypeFactory::build($className, $schema, $components);

        $models = [];
        if ($type instanceof ArrayType || $type instanceof ObjectType) {
            if ($parameter->in === 'path') {
                // Delegates to the type so that the existing, more precise message is kept.
                $type->getRouteRequirementPattern();
            }
            if ($parameter->in !== 'query') {
                throw new Exception('Array and object parameters are only supported in query.', $parameter->path);
            }
            if (!$isReference) {
                $collector = ModelCollector::forAggregate($bundleNamespace, $aggregateName, $components);
                $collector->collect($className, $schema);
                $models = $collector->getModels();
                foreach ($type->getUsedModelNames() as $usedModelName) {
                    $usedModelNames[] = $usedModelName;
                }
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
     * @return Stmt[]
     */
    public function getPathSanitizationStmts(): array
    {
        $f = new BuilderFactory();

        return [
            new Expression(new Assign(new Variable($this->variableName), new Variable($this->parameter->name))),
            new TryCatch([
                new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                    new Variable($this->variableName),
                    $this->getConstraintsAst(),
                ])),
            ], [
                new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var("{$this->parameter->in}Errors"), new String_($this->parameter->name)), $f->propertyFetch($f->var('e'), 'messages'))),
                ]),
            ]),
        ];
    }

    /**
     * @return Stmt[]
     */
    public function getNonPathSanitizationStmts(): array
    {
        $f = new BuilderFactory();

        return [
            new Expression(new Assign(new Variable($this->variableName), $this->type->getInitValue())),
            new TryCatch([
                new Expression(new Assign(new Variable($this->variableName), $this->getReadExpr())),
                new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                    new Variable($this->variableName),
                    $this->getConstraintsAst(),
                ])),
            ], [
                new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var("{$this->parameter->in}Errors"), new String_($this->parameter->name)), new Array_([new ArrayItem($f->methodCall($f->var('e'), 'getMessage'))], ['kind' => Array_::KIND_SHORT]))),
                ]),
                new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch($f->var("{$this->parameter->in}Errors"), new String_($this->parameter->name)), $f->propertyFetch($f->var('e'), 'messages'))),
                ]),
            ]),
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
                    ? new Expression(new Throw_($f->new('DenormalizationException', [new Encapsed([
                        new EncapsedStringPart('Parameter \''),
                        $f->var('name'),
                        new EncapsedStringPart('\' in \''),
                        $f->var('in'),
                        new EncapsedStringPart('\' is required.'),
                    ])])))
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
     * Populates the registry with the models this parameter needs, without keeping the statements:
     * they are emitted by getDenormalizerMethod, the models by the AbstractController.
     *
     * @throws Exception
     */
    public function registerDenormalizationModels(DenormalizationContext $context): void
    {
        if (!$this->isComplex()) {
            return;
        }

        $f = new BuilderFactory();
        $this->type->getParameterDenormalizationStmts($f->var('value'), $f->var('target'), $f->var('path'), $context);
    }

    private function isComplex(): bool
    {
        return $this->type instanceof ArrayType || $this->type instanceof ObjectType;
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

    private function getConstraintsAst(): Array_
    {
        return new Array_(array_map(
            static fn (Constraint $constraint): ArrayItem => new ArrayItem($constraint->getInstantiationAst()),
            $this->getConstraints(),
        ), ['kind' => Array_::KIND_SHORT]);
    }
}
