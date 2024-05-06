<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Printer\Printer;
use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Schema;

use function Symfony\Component\String\u;

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
        ?Components $components,
        bool $isComponent,
    ): self {
        $className = u($rawName)->camel()->title()->toString();

        $ordinal = 0;
        $ordinals = [];
        $attributes = [];
        foreach ($schema->properties as $rawPropertyName => $property) {
            $ordinals[$rawPropertyName] = ++$ordinal;
            $attributes[$rawPropertyName] = ModelAttribute::build($className, $rawPropertyName, $property, $components);
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
                if ($attribute->getUsedModelName() !== null) {
                    $usedModelNames[] = $attribute->getUsedModelName();
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
            $usedModelNames,
        );
    }

    /**
     * @param array<ModelAttribute> $attributes
     * @param array<string>         $usedFormatConstraintNames
     * @param array<string>         $usedModelNames
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

        $arrayAttributes = array_filter(
            $this->attributes,
            static fn (ModelAttribute $attribute) => $attribute->isArray(),
        );

        if (\count($arrayAttributes) > 0) {
            $comment = new PhpDocNode(array_map(
                static fn (ModelAttribute $attribute): PhpDocTagNode => $attribute->getDocAst(),
                $arrayAttributes,
            ));
            $constructor->setDocComment((new Printer())->print($comment));
        }

        $class = $f->class($this->className)
            ->addStmt($constructor)
        ;

        $namespace = $f->namespace($this->namespace)
            ->addStmt($f->use('Symfony\Component\Validator\Constraints')->as('Assert'))
        ;

        foreach ($this->usedFormatConstraintNames as $constraintName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\\Format\\{$constraintName}")->as("Assert{$constraintName}"));
        }

        foreach ($this->usedModelNames as $modelName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\\Model\\{$modelName}"));
        }

        $namespace->addStmt($class);

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareItem('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
