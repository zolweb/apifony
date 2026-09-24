<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Param;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Zol\Apifony\Resolved\Schema;
use Zol\Apifony\Resolved\SchemaRef;

class ModelAttribute
{
    /**
     * @throws Exception
     */
    public static function build(
        string $modelClassName,
        string $rawName,
        SchemaRef $property,
        bool $required,
    ): self {
        if (preg_match('/[^A-Za-z0-9_]/', $rawName)) {
            throw new Exception('Only [A-Za-z0-9_] are authorized chars in attribute names.', $property->path);
        }
        $className = "{$modelClassName}_{$rawName}";
        $isReference = $property->isReference;
        if ($isReference) {
            $className = (string) $property->getComponentName();
        }
        $schema = $property->getTarget();
        if ($required && $schema->hasDefault) {
            throw new Exception('Every required property must not have a default value.', $schema->path);
        }
        if (!$required && !$schema->hasDefault) {
            throw new Exception('Every non required property must have a default value.', $schema->path);
        }
        $className = Naming::forClass($className);
        $type = TypeFactory::build($className, $schema);

        // The component class this attribute is rendered as, if any. Null lets ObjectType recurse
        // into the type instead, which is how an inlined array or object reaches the models its
        // own items and properties reference.
        $usedModelName = $isReference && ModelCollector::producesModel($type) ? $className : null;

        return new self(
            $rawName,
            $schema,
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
