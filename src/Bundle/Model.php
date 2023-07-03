<?php

namespace Zol\Ogen\Bundle;

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
        $className = u($rawName)->camel()->title();

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
                $diff = (int)$attr1->hasDefault() - (int)$attr2->hasDefault();
                return $diff !== 0 ? $diff : $ordinals[$attr1->getRawName()] - $ordinals[$attr2->getRawName()];
            }
        );

        $usedFormatConstraintNames = [];
        foreach ($attributes as $attribute) {
            foreach ($attribute->getConstraints() as $constraint) {
                foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                    $usedFormatConstraintNames[$constraintName] = true ;
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
     * @param array<string> $usedFormatConstraintNames
     * @param array<string> $usedModelNames
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

    /**
     * @return string
     */
    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * @return array<ModelAttribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
    }

    /**
     * @return array<ModelAttribute>
     */
    public function getArrayAttributes(): array
    {
        return array_filter(
            $this->attributes,
            static fn (ModelAttribute $attribute) => $attribute->isArray(),
        );
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<string>
     */
    public function getUsedFormatConstraintNames(): array
    {
        return $this->usedFormatConstraintNames;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getName(): string
    {
        return "{$this->className}.php";
    }

    public function getParametersRootName(): string
    {
        return 'model';
    }

    public function getTemplate(): string
    {
        return 'model.php.twig';
    }
}