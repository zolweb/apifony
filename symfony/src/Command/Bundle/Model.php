<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Schema;
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
        Components $components,
    ): self {
        $className = u($rawName)->camel()->title();

        $attributes = array_map(
            static fn (string $rawPropertyName) =>
            ModelAttribute::build($className, $rawPropertyName, $schema->properties[$rawPropertyName], $components),
            array_keys($schema->properties),
        );

        $ordinal = 0;
        $ordinals = [];
        foreach ($schema->properties as $rawPropertyName => $property) {
            $ordinals[$rawPropertyName] = ++$ordinal;
        }

        usort(
            $attributes,
            static fn (ModelAttribute $attr1, ModelAttribute $attr2) =>
            ((int)$attr1->hasDefault() - (int)$attr2->hasDefault()) ?:
                ($ordinals[$attr1->getRawName()] - $ordinals[$attr2->getRawName()]),
        );

        $usedFormatConstraintNames = [];
        foreach ($attributes as $attribute) {
            foreach ($attribute->getConstraints() as $constraint) {
                foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                    $usedFormatConstraintNames[$constraintName] = true ;
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
        );
    }

    /**
     * @param array<ModelAttribute> $attributes
     * @param array<string> $usedFormatConstraintNames
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $namespace,
        private readonly string $folder,
        private readonly string $className,
        private readonly array $attributes,
        private readonly array $usedFormatConstraintNames,
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
     * @return array<FormatConstraint>
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