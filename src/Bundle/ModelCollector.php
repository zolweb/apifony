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

        if (isset($this->models[$rawName])) {
            return;
        }

        $type = TypeFactory::build('', $schema, $this->components);

        if ($type instanceof ObjectType) {
            if ($schema->extensions['x-apifony-raw'] ?? false) {
                return;
            }
            $this->models[$rawName] = Model::build(
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
     * @return list<Model>
     */
    public function getModels(): array
    {
        return array_values($this->models);
    }
}
