<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Ogen\OpenApi\Schema;
use function Symfony\Component\String\u;

class BooleanType implements Type
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

    public function getPhpDocParameterAnnotationType(): string
    {
        return 'bool';
    }

    public function getMethodParameterType(): string
    {
        return 'bool';
    }

    public function getMethodParameterDefault(): ?string
    {
        return [true => 'true', false => 'false', null => null][$this->schema->default];
    }

    public function getRouteRequirementPattern(): string
    {
        return 'true|false';
    }

    public function getStringToTypeCastFunction(): string
    {
        return 'boolval';
    }

    public function getNormalizedType(): string
    {
        return 'Boolean';
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return 'is_bool($requestBodyPayload)';
    }

    public function getRequestBodyPayloadTypeCheckingAst(): Expr
    {
        $f = new BuilderFactory();

        return $f->funcCall('is_bool', [$f->var('requestBodyPayload')]);
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if (!$this->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        if (count($this->schema->enum) > 0) {
            $constraints[] = new Constraint('Assert\Choice', ['choices' => $this->schema->enum]);
        }

        return $constraints;
    }

    public function getBuiltInPhpType(): string
    {
        return 'bool';
    }

    public function getInitValue(): bool
    {
        return false;
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    public function getDocAst(): TypeNode
    {
        $type = new IdentifierTypeNode('bool');

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }
}