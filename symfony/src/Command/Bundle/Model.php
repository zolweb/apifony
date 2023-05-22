<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class Model implements PhpClassFile
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $name,
        Schema $schema,
        Components $components,
    ): self {
        $className = u($name)->camel()->title();

        $attributes = array_map(
            static fn (string $propertyName) =>
            ModelAttribute::build($className, $propertyName, $schema->properties[$propertyName], $components),
            array_keys($schema->properties),
        );

        $ordinal = 0;
        $ordinals = [];
        foreach ($schema->properties as $propertyName => $property) {
            $ordinals[$propertyName] = ++$ordinal;
        }

        usort(
            $attributes,
            static fn (ModelAttribute $attr1, ModelAttribute $attr2) =>
            ((int)($attr1->hasDefault()) - (int)($attr2->hasDefault())) ?:
                ($ordinals[$attr1->getPropertyName()] - $ordinals[$attr2->getPropertyName()]),
        );

        return new self(
            $bundleNamespace,
            $className,
            $attributes,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $className,
        private readonly array $attributes,
    ) {
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

    public function getFolder(): string
    {
        return 'src/Model';
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

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Payload";
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getUsedPhpClassFiles(): array
    {
        return [];
    }
}