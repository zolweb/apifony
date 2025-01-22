<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Zol\Ogen\OpenApi\Schema;

use function Symfony\Component\String\u;

class ObjectType implements Type
{
    private readonly bool $isRaw;

    public function __construct(
        private readonly Schema $schema,
        private readonly bool $nullable,
        private readonly string $name,
    ) {
        $this->isRaw = ($this->schema->extensions['x-raw'] ?? false) === true;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getDefaultExpr(): Expr
    {
        throw new \RuntimeException();
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Object path parameters are not supported.');
    }

    public function getNormalizedType(): string
    {
        return $this->isRaw ? 'mixed' : $this->name;
    }

    public function getRequestBodyPayloadTypeCheckingAst(): Expr
    {
        $f = new BuilderFactory();

        return $this->isRaw ?
            $f->funcCall('is_array', [$f->var('requestBodyPayload')]) :
            new Expr\Instanceof_($f->var('requestBodyPayload'), new Name($this->name));
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

    public function getBuiltInPhpType(): string
    {
        return $this->isRaw ? 'mixed' : 'object';
    }

    public function getInitValue(): Expr
    {
        throw new \RuntimeException('Can not init object');
    }

    public function getUsedModel(): ?string
    {
        return null;
    }

    public function getDocAst(): TypeNode
    {
        $type = $this->isRaw ?
            new IdentifierTypeNode('mixed') :
            new IdentifierTypeNode($this->name);

        if ($this->nullable) {
            $type = new NullableTypeNode($type);
        }

        return $type;
    }

    public function asName(): Name
    {
        return new Name(($this->nullable ? '?' : '').($this->isRaw ? 'mixed' : $this->name));
    }
}
