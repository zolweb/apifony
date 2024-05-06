<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Param;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

use function Symfony\Component\String\u;

class ModelAttribute
{
    /**
     * @throws Exception
     */
    public static function build(
        string $modelClassName,
        string $rawName,
        Reference|Schema $property,
        ?Components $components,
    ): self {
        $usedModelName = null;
        $variableName = u($rawName)->camel()->toString();
        $className = "{$modelClassName}_{$variableName}";
        if ($property instanceof Reference) {
            if ($components === null || !isset($components->schemas[$property->getName()])) {
                throw new Exception('Reference not found in schemas components.');
            }
            $property = $components->schemas[$className = $usedModelName = $property->getName()];
        }
        $className = u($className)->camel()->title()->toString();
        $type = TypeFactory::build($className, $property, $components);
        if ($type instanceof ArrayType) {
            $usedModelName = $type->getUsedModel();
        }

        return new self(
            $rawName,
            $variableName,
            $property,
            $type,
            $usedModelName,
        );
    }

    private function __construct(
        private readonly string $rawName,
        private readonly string $variableName,
        private readonly Schema $schema,
        private readonly Type $type,
        private readonly ?string $usedModelName,
    ) {
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
    }

    public function isArray(): bool
    {
        return $this->schema->type === 'array' || ($this->schema->type === 'object' && ($this->schema->extensions['x-raw'] ?? false));
    }

    public function hasDefault(): bool
    {
        return $this->schema->default !== null;
    }

    public function getDefault(): Expr
    {
        return $this->type->getMethodParameterDefault();
    }

    public function getRawName(): string
    {
        return $this->rawName;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function isNullable(): bool
    {
        return $this->type->isNullable();
    }

    public function getTypeDocAst(): TypeNode
    {
        return $this->type->getDocAst();
    }

    public function getDocAst(): PhpDocTagNode
    {
        return new PhpDocTagNode('@param', new ParamTagValueNode($this->type->getDocAst(), false, "\${$this->variableName}", ''));
    }

    public function getParamAst(): Param
    {
        $f = new BuilderFactory();

        $param = $f->param($this->variableName)
            ->setType($this->type->asName())
            ->makePublic()
            ->makeReadonly()
        ;

        if ($this->schema->default !== null) {
            $param->setDefault($this->schema->default);
        }

        foreach ($this->type->getConstraints() as $constraint) {
            $param->addAttribute($constraint->getAttributeAst());
        }

        return $param->getNode();
    }
}
