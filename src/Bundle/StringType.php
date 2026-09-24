<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\Narrow;
use Zol\Apifony\Resolved\Schema;

class StringType implements Type
{
    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
    ) {
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

        if (!\is_string($this->schema->default)) {
            throw new \RuntimeException();
        }

        return new String_($this->schema->default);

        // todo check in schema
        // if (is_null($this->schema->default)) {
        //     if (!$this->nullable) {
        //         throw new Exception('Schemas that are not nullable cannot have null default.');
        //     }
        //     return 'null';
        // }
    }

    public function getRouteRequirementPattern(): string
    {
        // The # character is written in the escaped sequence \x{0023} in order to prevent missing escape in regex at
        // line 180 in Symfony\Component\Routing\Generator\UrlGenerator.
        return $this->schema->pattern !== null ? $this->schema->pattern : '[^:/?\x{0023}[\]@!$&\'\'()*+,;=]+'; // TODO Remove one of the \' and write a twig escaper for yaml strings
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

        if ($this->schema->pattern !== null) {
            $constraints[] = new Constraint('Assert\Regex', ['pattern' => $this->schema->pattern]);
        }

        if ($this->schema->minLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['min' => $this->schema->minLength]);
        }

        if ($this->schema->maxLength !== null) {
            $constraints[] = new Constraint('Assert\Length', ['max' => $this->schema->maxLength]);
        }

        if (\count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum], enforcedByDenormalizer: true);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'string';
    }

    public function getInitValue(): Expr
    {
        return new String_('');
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    public function getDocAst(): TypeNode
    {
        if (\count($this->schema->enum) > 0) {
            $enum = array_map(static fn (mixed $e) => Narrow::stringOrNull($e), $this->schema->enum);

            return new IdentifierTypeNode(
                implode('|', array_map(static fn (?string $value) => $value !== null ? "'{$value}'" : 'null', $enum)),
            );
        }

        $type = new IdentifierTypeNode('string');

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function getUsedModelNames(): array
    {
        return [];
    }

    public function hasInformativeDocType(): bool
    {
        return false;
    }

    public function getDenormalizationRootModels(): array
    {
        return [];
    }

    public function getParameterDenormalizationStmts(Expr $source, Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        return $context->wrapNullable($this->nullable, $source, $target, fn (Expr $value): array => array_merge(
            [new Expression(new Assign($target, $f->methodCall($f->var('this'), \sprintf('denormalize%s%s', ucfirst($this->getBuiltInPhpType()), $context->getSource()), [$value, $path])))],
            $this->getNarrowingStmts($target, $path, $context),
        ));
    }

    /**
     * Statements making the value actually satisfy the narrowed type getDocAst() advertises, so
     * that the generated models can be constructed without asserting anything.
     *
     * @return list<Stmt>
     */
    private function getNarrowingStmts(Expr $target, Expr $path, DenormalizationContext $context): array
    {
        $f = new BuilderFactory();

        if (\count($this->schema->enum) === 0) {
            return [];
        }

        $expectation = \sprintf('This value should be one of %s.', implode(', ', array_map(
            static fn (mixed $e): string => $e === null ? 'null' : var_export($e, true),
            $this->schema->enum,
        )));

        return [new If_(
            new BooleanNot($f->funcCall('\in_array', [
                $target,
                new Array_(array_map(static fn (mixed $e): ArrayItem => new ArrayItem($f->val($e)), $this->schema->enum), ['kind' => Array_::KIND_SHORT]),
                $f->val(true),
            ])),
            ['stmts' => [new Expression(new Throw_($f->new('DenormalizationException', [
                $path, $f->val('invalid_enum_value'), $f->val($expectation),
            ])))]],
        )];
    }

    public function asName(): Name
    {
        return new Name($this->nullable ? '?string' : 'string');
    }
}
