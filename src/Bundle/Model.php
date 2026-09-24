<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Printer\Printer;
use Zol\Apifony\Resolved\Schema;

class Model implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $namespace,
        string $folder,
        string $rawName,
        Schema $schema,
        bool $isComponent,
        NameRegistry $names,
    ): self {
        $className = Naming::forClass($rawName);

        $ordinal = 0;
        $ordinals = [];
        $attributes = [];
        foreach ($schema->properties as $rawPropertyName => $property) {
            $names->claimProperty("{$namespace}\\{$className}", $rawPropertyName, Origin::spec('property', $rawPropertyName, $property->path));
            $ordinals[$rawPropertyName] = ++$ordinal;
            $attributes[$rawPropertyName] = ModelAttribute::build(
                $className,
                $rawPropertyName,
                $property,
                \in_array($rawPropertyName, $schema->required, true),
            );
        }

        usort(
            $attributes,
            static function (ModelAttribute $attr1, ModelAttribute $attr2) use ($ordinals) {
                $diff = (int) $attr1->hasDefault() - (int) $attr2->hasDefault();

                return $diff !== 0 ? $diff : $ordinals[$attr1->getRawName()] - $ordinals[$attr2->getRawName()];
            }
        );

        $usedFormatConstraintNames = [];
        foreach ($attributes as $attribute) {
            foreach ($attribute->getConstraints() as $constraint) {
                foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                    $usedFormatConstraintNames[$constraintName] = true;
                }
            }
        }

        $usedModelNames = [];
        if (!$isComponent) {
            foreach ($attributes as $attribute) {
                foreach ($attribute->getUsedModelNames() as $usedModelName) {
                    $usedModelNames[$usedModelName] = true;
                }
            }
        }

        return new self(
            $bundleNamespace,
            $namespace,
            $folder,
            $className,
            $attributes,
            array_keys($usedFormatConstraintNames),
            array_keys($usedModelNames),
        );
    }

    /**
     * @param list<ModelAttribute> $attributes
     * @param list<string>         $usedFormatConstraintNames
     * @param list<string>         $usedModelNames
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $namespace,
        private readonly string $folder,
        private readonly string $className,
        private readonly array $attributes,
        private readonly array $usedFormatConstraintNames,
        private readonly array $usedModelNames,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getName(): string
    {
        return "{$this->className}.php";
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $constructor = $f->method('__construct')
            ->makePublic()
        ;

        foreach ($this->attributes as $attribute) {
            $constructor->addParam($attribute->getParam());
        }

        $comment = new PhpDocNode(array_map(
            static fn (ModelAttribute $attribute): PhpDocTagNode => $attribute->getDocAst(),
            $this->attributes,
        ));

        $constructor->setDocComment((new Printer())->print($comment));

        $class = $f->class($this->className)
            ->addStmt($constructor)
        ;

        $namespace = $f->namespace($this->namespace);

        // A model whose every constraint was PHP's own to enforce renders no Assert attribute.
        foreach ($this->attributes as $attribute) {
            foreach ($attribute->getAttributeConstraints() as $constraint) {
                if (str_starts_with($constraint->getName(), 'Assert\\')) {
                    $namespace->addStmt($f->use('Symfony\Component\Validator\Constraints')->as('Assert'));

                    break 2;
                }
            }
        }

        foreach ($this->usedFormatConstraintNames as $constraintName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\\Format\\{$constraintName}")->as("Assert{$constraintName}"));
        }

        foreach ($this->usedModelNames as $modelName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\\Model\\{$modelName}"));
        }

        $namespace->addStmt($class);

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
