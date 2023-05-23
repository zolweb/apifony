<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Header;
use App\Command\OpenApi\OpenApi;
use App\Command\OpenApi\Parameter;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\RequestBody;
use App\Command\OpenApi\Response;
use App\Command\OpenApi\Schema;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use function Symfony\Component\String\u;

class Bundle // extends AbstractExtension
{
    /**
     * @throws Exception
     */
    public static function build(
        string $namespace,
        OpenApi $openApi,
    ): self {
        return new self(
            self::extractModels($namespace, $openApi),
            self::extractFormats($namespace, $openApi),
        );
    }

    /**
     * @param array<Model> $models
     */
    private function __construct(
        private readonly array $models,
        private readonly array $formats,
    ) {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->formats as $format) {
            $files[] = $format['definition'];
            $files[] = $format['constraint'];
            $files[] = $format['validator'];
        }

        foreach ($this->models as $model) {
            $files[] = $model;
        }

        // $this->api->addFiles($files);

        return $files;
    }

    /**
     * @return array<Model>
     *
     * @throws Exception
     */
    private static function extractModels(string $namespace, OpenApi $openApi): array
    {
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
                    $addModels($name, $schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas as $name => $schema) {
            $addModels($name, $schema);
        }

        return $models;
    }

    /**
     * @return array<string, array{definition: FormatDefinition, constraint: FormatConstraint, validator: FormatValidator}>
     */
    private static function extractFormats(string $namespace, OpenApi $openApi): array
    {
        $formats = [];

        $addSchemaFormats = function (Reference|Schema $schema) use (&$addSchemaFormats, &$formats, $openApi) {
            if ($schema instanceof Schema) {
                if ($schema->format !== null) {
                    $formats[$schema->format] = [];
                }
                foreach ($schema->properties ?? [] as $property) {
                    $addSchemaFormats($property);
                }
                if ($schema->items !== null) {
                    $addSchemaFormats($schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas as $schema) {
            $addSchemaFormats($schema);
        }
        foreach ($openApi->components->parameters as $parameter) {
            $addSchemaFormats($parameter->schema);
        }
        foreach ($openApi->components->requestBodies as $requestBody) {
            foreach ($requestBody->mediaTypes as $mediaType) {
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->responses as $response) {
            foreach ($response->headers as $header) {
                if ($header instanceof Header) {
                    $addSchemaFormats($header->schema);
                }
            }
            foreach ($response->content as $mediaType) {
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->headers as $header) {
            $addSchemaFormats($header->schema);
        }
        foreach ($openApi?->paths->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->parameters as $parameter) {
                if ($parameter instanceof Parameter) {
                    $addSchemaFormats($parameter->schema);
                }
            }
            foreach ($pathItem->operations as $operation) {
                foreach ($operation->parameters as $parameter) {
                    if ($parameter instanceof Parameter) {
                        $addSchemaFormats($parameter->schema);
                    }
                }
                if ($operation->requestBody instanceof RequestBody) {
                    foreach ($operation->requestBody->mediaTypes as $mediaType) {
                        $addSchemaFormats($mediaType->schema);
                    }
                }
                foreach ($operation?->responses->responses ?? [] as $response) {
                    if ($response instanceof Response) {
                        foreach ($response->headers as $header) {
                            if ($header instanceof Header) {
                                $addSchemaFormats($header->schema);
                            }
                        }
                        foreach ($response->content as $mediaType) {
                            $addSchemaFormats($mediaType->schema);
                        }
                    }
                }
            }
        }

        foreach ($formats as $name => &$format) {
            $format['definition'] = FormatDefinition::build($namespace, $name);
            $format['constraint'] = FormatConstraint::build($namespace, $name);
            $format['validator'] = FormatValidator::build($namespace, $name, $format['definition']);
        }

        return $formats;
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