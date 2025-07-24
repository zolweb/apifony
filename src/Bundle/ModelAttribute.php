<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Param;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

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
        bool $required,
        ?Components $components,
    ): self {
        $usedModelName = null;
        if (preg_match('/[^A-Za-z0-9_]/', $rawName)) {
            throw new Exception('Only [A-Za-z0-9_] are authorized chars in attribute names.', $property->path);
        }
        $className = "{$modelClassName}_{$rawName}";
        if ($property instanceof Reference) {
            if ($components === null || !isset($components->schemas[$property->getName()])) {
                throw new Exception('Reference not found in schemas components.', $property->path);
            }
            $property = $components->schemas[$className = $usedModelName = $property->getName()];
        }
        if ($required && $property->hasDefault) {
            throw new Exception('Every required property must not have a default value.', $property->path);
        }
        if (!$required && !$property->hasDefault) {
            throw new Exception('Every non required property must have a default value.', $property->path);
        }
        $className = u($className)->camel()->title()->toString();
        $type = TypeFactory::build($className, $property, $components);
        if ($type instanceof ArrayType) {
            $usedModelName = $type->getUsedModel();
        }

        return new self(
            $rawName,
            $property,
            $type,
            $usedModelName,
        );
    }

    private function __construct(
        private readonly string $rawName,
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

    public function getRawName(): string
    {
        return $this->rawName;
    }

    /**
     * @return list<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function getDocAst(): PhpDocTagNode
    {
        return new PhpDocTagNode('@param', new ParamTagValueNode($this->type->getDocAst(), false, "\${$this->rawName}", '', false));
    }

    public function getParam(): Param
    {
        $f = new BuilderFactory();

        $param = $f->param($this->rawName)
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

        // Little hack making the printer print each attribute on a new line
        $node = $param->getNode();
        $node->setDocComment(new Doc(''));

        return $node;
    }
}
