<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Zol\Apifony\Resolved\SchemaRef;

/**
 * Walks a schema tree and builds one Model per object schema found in it.
 */
class ModelCollector
{
    /**
     * @var array<string, Model>
     */
    private array $models = [];

    private function __construct(
        private readonly NameRegistry $names,
        private readonly string $bundleNamespace,
        private readonly string $namespace,
        private readonly string $folder,
        private readonly bool $isComponent,
        private readonly bool $followReferences,
    ) {
    }

    /**
     * Collects the models of the components schemas, following references.
     */
    public static function forComponents(string $bundleNamespace, NameRegistry $names): self
    {
        return new self(
            $names,
            $bundleNamespace,
            "{$bundleNamespace}\\Model",
            'src/Model',
            true,
            true,
        );
    }

    /**
     * Collects the models inlined in an aggregate. Referenced schemas are skipped, as they are
     * already emitted as components.
     */
    public static function forAggregate(string $bundleNamespace, string $aggregateName, NameRegistry $names): self
    {
        return new self(
            $names,
            $bundleNamespace,
            "{$bundleNamespace}\\Api\\{$aggregateName}",
            "src/Api/{$aggregateName}",
            false,
            false,
        );
    }

    /**
     * @throws Exception
     */
    public function collect(string $rawName, SchemaRef $ref, ?Origin $origin = null): void
    {
        if ($ref->isReference) {
            if (!$this->followReferences) {
                return;
            }
            // A different named thing, so it claims its class under its own identity rather than
            // the one the caller was carrying.
            $rawName = (string) $ref->getComponentName();
            $origin = null;
        }
        $schema = $ref->getTarget();

        $className = Naming::forClass($rawName);
        $origin ??= Origin::spec('schema', $rawName, $schema->path);
        $this->names->claimClass($this->namespace, $className, $origin);
        if (isset($this->models[$className])) {
            return;
        }

        $type = TypeFactory::build('', $schema);

        if ($type instanceof ObjectType) {
            $this->models[$className] = Model::build(
                $this->bundleNamespace,
                $this->namespace,
                $this->folder,
                $rawName,
                $schema,
                $this->isComponent,
                $this->names,
            );
            foreach ($schema->properties as $propertyName => $property) {
                $this->collect("{$rawName}_{$propertyName}", $property);
            }
        } elseif ($type instanceof ArrayType) {
            if ($schema->items === null) {
                throw new Exception('Schema objects of array type without items attribute are not supported.', $schema->path);
            }
            // Unwrapping an array level is the same named thing seen one level down, not a second
            // claimant on the name, so the identity is carried along.
            $this->collect($rawName, $schema->items, $origin);
        }
    }

    /**
     * The rule collect() applies, asked of a type instead of a schema: a reference is rendered as
     * a class of its own only when unwrapping its array levels ends on an inline object schema. An
     * array whose items are a reference lends its name to nothing, the referenced schema owns the
     * class; a scalar or a raw schema is inlined and names nothing at all.
     *
     * @throws Exception
     */
    public static function producesModel(Type $type): bool
    {
        while ($type instanceof ArrayType) {
            if ($type->getUsedModel() !== null) {
                return false;
            }
            $type = $type->getItemType();
        }

        return $type instanceof ObjectType;
    }

    /**
     * @return list<Model>
     */
    public function getModels(): array
    {
        return array_values($this->models);
    }
}
