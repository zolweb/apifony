<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use Zol\Apifony\OpenApi\Components;
use Zol\Apifony\OpenApi\Reference;
use Zol\Apifony\OpenApi\Schema;

/**
 * Walks a schema tree and builds one Model per object schema found in it.
 */
class ModelCollector
{
    /**
     * @var array<string, Model>
     */
    private array $models = [];

    /**
     * The specification name each generated class came from, to report a collapse.
     *
     * @var array<string, string>
     */
    private array $sources = [];

    private function __construct(
        private readonly ?Components $components,
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
    public static function forComponents(string $bundleNamespace, ?Components $components): self
    {
        return new self(
            $components,
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
    public static function forAggregate(string $bundleNamespace, string $aggregateName, ?Components $components): self
    {
        return new self(
            $components,
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
    public function collect(string $rawName, Reference|Schema $schema): void
    {
        if ($schema instanceof Reference) {
            if (!$this->followReferences) {
                return;
            }
            if ($this->components === null || !isset($this->components->schemas[$schema->getName()])) {
                throw new Exception('Reference not found in schemas components.', $schema->path);
            }
            $schema = $this->components->schemas[$rawName = $schema->getName()];
        }

        $className = Naming::forClass($rawName);
        Naming::assertIdentifier($className, \sprintf('Schema \'%s\'', $rawName), $schema->path);
        if (isset($this->models[$className])) {
            if ($this->sources[$className] !== $rawName) {
                throw new Exception(\sprintf('Schemas \'%s\' and \'%s\' both map to the \'%s\' model.', $this->sources[$className], $rawName, $className), $schema->path);
            }

            return;
        }

        $type = TypeFactory::build('', $schema, $this->components);

        if ($type instanceof ObjectType) {
            $this->sources[$className] = $rawName;
            $this->models[$className] = Model::build(
                $this->bundleNamespace,
                $this->namespace,
                $this->folder,
                $rawName,
                $schema,
                $this->components,
                $this->isComponent,
            );
            foreach ($schema->properties as $propertyName => $property) {
                $this->collect("{$rawName}_{$propertyName}", $property);
            }
        } elseif ($type instanceof ArrayType) {
            if ($schema->items === null) {
                throw new Exception('Schema objects of array type without items attribute are not supported.', $schema->path);
            }
            $this->collect($rawName, $schema->items);
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
