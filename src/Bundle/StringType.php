<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Apifony\OpenApi\Schema;

use function Symfony\Component\String\u;

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

    public function getRequestBodyPayloadTypeCheckingAst(): Expr
    {
        $f = new BuilderFactory();

        return $f->funcCall('is_string', [$f->var('requestBodyPayload')]);
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
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
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
            return new IdentifierTypeNode(
                implode(
                    '|',
                    array_map(
                        static fn (?string $value) => $value !== null ? "'{$value}'" : 'null',
                        $this->schema->enum,
                    ),
                ),
            );
        }

        $type = new IdentifierTypeNode('string');

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function asName(): Name
    {
        return new Name($this->nullable ? '?string' : 'string');
    }
}
