<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\OpenApi;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;

class Bundle
{
    public static function build(
        string $namespace,
        OpenApi $openApi,
    ): self {
        $payloads = [];

        $addPayloads = function(string $name, Reference|Schema $schema) use (&$addPayloads, &$payloads, $namespace, $openApi) {
            if ($schema instanceof Reference) {
                $schema = $openApi->components->schemas[$name = $schema->getName()];
            }
            if (!isset($payloads[$name])) {
                if ($schema->type === 'object') {
                    $payloads[$name] = Payload::build($namespace, $name, $schema);
                    foreach ($schema->properties as $propertyName => $property) {
                        $addPayloads("{$name}{$propertyName}", $property);
                    }
                } elseif ($schema->type === 'array') {
                    $addPayloads("{$name}List", $schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas as $name => $schema) {
            $addPayloads($name, $schema);
        }

        return new self(
            Api::build($namespace, $openApi),
            $payloads,
        );
    }

    /**
     * @param array<Payload> $payloads
     */
    private function __construct(
        private readonly Api $api,
        private readonly array $payloads,
    ) {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = $this->payloads;

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
}