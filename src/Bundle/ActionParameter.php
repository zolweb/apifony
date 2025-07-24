<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TryCatch;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Parameter;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

use function Symfony\Component\String\u;

class ActionParameter
{
    /**
     * @throws Exception
     */
    public static function build(
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
        if ($parameter->schema instanceof Reference) {
            throw new \RuntimeException();
        }
        if ($parameter->required && $parameter->schema->hasDefault) {
            throw new Exception('Every required parameter must not have a default value.', $parameter->path);
        }
        if (!$parameter->required && !$parameter->schema->hasDefault) {
            throw new Exception('Every non required parameter must have a default value.', $parameter->path);
        }
        $variableName = \sprintf('%s%s', $parameter->in[0], u($parameter->name)->camel()->title());
        $className = "{$actionClassName}_{$parameter->name}";
        $className = u($className)->camel()->title()->toString();

        return new self(
            $variableName,
            $parameter,
            TypeFactory::build($className, $parameter->schema, $components),
            $parameter->schema,
            $ordinal,
        );
    }

    private function __construct(
        private readonly string $variableName,
        private readonly Parameter $parameter,
        private readonly Type $type,
        private readonly Schema $schema,
        private readonly int $ordinal,
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
                    array_map(
                        static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                        $this->getConstraints(),
                    ),
                ])),
            ], [
                new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($this->parameter->in)), new String_($this->parameter->name)), $f->propertyFetch($f->var('e'), 'messages'))),
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
                new Expression(new Assign(new Variable($this->variableName), $f->methodCall($f->var('this'), \sprintf('get%s%sParameter', ucfirst($this->type->getBuiltInPhpType()), $this->type->isNullable() ? 'OrNull' : ''), array_merge([$f->var('request'), new String_($this->parameter->name), $this->parameter->in, $this->parameter->required], $this->schema->hasDefault ? [$this->type->getDefaultExpr()] : [])))),
                new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                    new Variable($this->variableName),
                    array_map(
                        static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                        $this->getConstraints(),
                    ),
                ])),
            ], [
                new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($this->parameter->in)), new String_($this->parameter->name)), new Array_([new ArrayItem($f->methodCall($f->var('e'), 'getMessage'))]))),
                ]),
                new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                    new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($this->parameter->in)), new String_($this->parameter->name)), $f->propertyFetch($f->var('e'), 'messages'))),
                ]),
            ]),
        ];
    }
}
