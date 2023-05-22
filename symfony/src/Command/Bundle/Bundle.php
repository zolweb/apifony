<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\OpenApi;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use function Symfony\Component\String\u;

class Bundle // extends AbstractExtension
{
    public static function build(
        string $namespace,
        OpenApi $openApi,
    ): self {
        $addModels = function(string $name, Reference|Schema $schema) use (&$addModels, &$models, $namespace, $openApi) {
            if ($schema instanceof Reference) {
                $schema = $openApi->components->schemas[$name = $schema->getName()];
            }
            if (!isset($models[$name])) {
                if ($schema->type === 'object') {
                    $models[$name] = Model::build($namespace, $name, $schema, $openApi->components);
                    foreach ($schema->properties as $propertyName => $property) {
                        $addModels("{$name}_{$propertyName}", $property);
                    }
                } elseif ($schema->type === 'array') {
                    $addModels("{$name}List", $schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas as $name => $schema) {
            $addModels($name, $schema);
        }

        return new self(
            Api::build($namespace, $openApi),
            $models,
            $openApi->components ?? Components::build([]),
        );
    }

    /**
     * @param array<Model> $models
     */
    private function __construct(
        private readonly Api $api,
        private readonly array $models,
        private readonly Components $components,
    ) {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = $this->models;

        // $this->api->addFiles($files);

        return $files;
    }

    // /**
    //  * @return array<Operation>
    //  */
    // public function getAllSortedOperations(): array
    // {
    //     $operations = array_merge(
    //         ...array_map(
    //         static fn (PathItem $pathItem) => $pathItem->operations,
    //         $this->pathItems,
    //     ),
    //     );

    //     usort(
    //         $operations,
    //         static fn (Operation $operation1, Operation $operation2) =>
    //         $operation2->priority - $operation1->priority ?:
    //             strcmp($operation1->operationId, $operation2->operationId),
    //     );

    //     return $operations;
    // }

    // public function getFilters(): array
    // {
    //     return [
    //         // new TwigFilter('type', [$this, 'getType']),
    //     ];
    // }

    // public function getType(Reference|Schema $schema, string $name = ''): Type
    // {
    //     if ($schema instanceof Reference) {
    //         $schema = $this->components->schemas[$name = $schema->getName()];
    //     }
    //     return match ($schema->type) {
    //         'string' => new StringType($schema),
    //         'integer' => new IntegerType($schema),
    //         'number' => new NumberType($schema),
    //         'boolean' => new BooleanType($schema),
    //         'object' => new ObjectType($schema, $name, $this->components),
    //         'array' => new ArrayType($schema, $this->components),
    //     };
    // }
}