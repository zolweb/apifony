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
        if (preg_match('/[^A-Za-z0-9_]/', $rawName)) {
            throw new Exception('Only [A-Za-z0-9_] are authorized chars in attribute names.', $property->path);
        }
        $className = "{$modelClassName}_{$rawName}";
        $isReference = false;
        if ($property instanceof Reference) {
            if ($components === null || !isset($components->schemas[$property->getName()])) {
                throw new Exception('Reference not found in schemas components.', $property->path);
            }
            $isReference = true;
            $property = $components->schemas[$className = $property->getName()];
        }
        if ($required && $property->hasDefault) {
            throw new Exception('Every required property must not have a default value.', $property->path);
        }
        if (!$required && !$property->hasDefault) {
            throw new Exception('Every non required property must have a default value.', $property->path);
        }
        $className = Naming::forClass($className);
        $type = TypeFactory::build($className, $property, $components);

        // The component class this attribute is rendered as, if any. Null lets ObjectType recurse
        // into the type instead, which is how an inlined array or object reaches the models its
        // own items and properties reference.
        $usedModelName = $isReference && ModelCollector::producesModel($type) ? $className : null;

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

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * The models the file holding this attribute must import: the component class the attribute is
     * rendered as, or, when it is inlined, whatever its own type reaches. Recursion stops at a
     * component, which imports its own dependencies.
     *
     * @return list<string>
     *
     * @throws Exception
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelName !== null ? [$this->usedModelName] : $this->type->getUsedModelNames();
    }

    public function hasDefault(): bool
    {
        return $this->schema->hasDefault;
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

    /**
     * The constraints actually rendered as attributes, once PHP's own guarantees are taken out.
     *
     * @return list<Constraint>
     */
    public function getAttributeConstraints(): array
    {
        return Constraint::filterPhpGuaranteed($this->type->getConstraints());
    }

    public function getParam(): Param
    {
        $f = new BuilderFactory();

        $param = $f->param($this->rawName)
            ->setType($this->type->asName())
            ->makePublic()
            ->makeReadonly()
        ;

        if ($this->schema->hasDefault) {
            $param->setDefault($this->type->getDefaultExpr());
        }

        foreach ($this->getAttributeConstraints() as $constraint) {
            $param->addAttribute($constraint->getAttributeAst());
        }

        // Little hack making the printer print each attribute on a new line
        $node = $param->getNode();
        $node->setDocComment(new Doc(''));

        return $node;
    }
}
