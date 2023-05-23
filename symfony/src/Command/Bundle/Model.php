<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class Model implements File
{
    /**
     * @param array<string, Format> $formats
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $rawName,
        Schema $schema,
        Components $components,
        array $formats,
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
            ((int)($attr1->hasDefault()) - (int)($attr2->hasDefault())) ?:
                ($ordinals[$attr1->getRawName()] - $ordinals[$attr2->getRawName()]),
        );

        $usedFormatConstraints = [];
        foreach ($schema->properties as $property) {
            if ($property instanceof Reference) {
                $property = $components->schemas[$property->getName()];
            }
            if ($property->format !== null) {
                $usedFormatConstraints[] = $formats[$property->format]->getConstraint();
            }
            if ($property->items !== null) {
                $items = $property->items;
                if ($items instanceof Reference) {
                    $items = $components->schemas[$items->getName()];
                }
                if ($items->format !== null) {
                    $usedFormatConstraints[] = $formats[$items->format]->getConstraint();
                }
            }
        }

        return new self(
            $bundleNamespace,
            $className,
            $attributes,
            $usedFormatConstraints,
        );
    }

    /**
     * @param array<ModelAttribute> $attributes
     * @param array<FormatConstraint> $usedFormatConstraints
     */
    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $className,
        private readonly array $attributes,
        private readonly array $usedFormatConstraints,
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

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Payload";
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<FormatConstraint>
     */
    public function getUsedFormatConstraints(): array
    {
        return $this->usedFormatConstraints;
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
}