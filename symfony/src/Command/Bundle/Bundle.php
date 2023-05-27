<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Header;
use App\Command\OpenApi\OpenApi;
use App\Command\OpenApi\Parameter;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\RequestBody;
use App\Command\OpenApi\Response;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class Bundle implements File
{
    /**
     * @throws Exception
     */
    public static function build(
        string $rawName,
        string $namespace,
        OpenApi $openApi,
    ): self {
        return new self(
            u($rawName)->camel()->title(),
            $namespace,
            self::buildFormats($namespace, $openApi),
            self::buildModels($namespace, $openApi),
            $api = Api::build($namespace, $openApi),
            RoutesConfig::build($namespace, $api),
            ServicesConfig::build($namespace, $api),
        );
    }

    /**
     * @param array<string, Format> $formats
     * @param array<Model> $models
     */
    private function __construct(
        private readonly string $name,
        private readonly string $namespace,
        private readonly array $formats,
        private readonly array $models,
        private readonly Api $api,
        private readonly RoutesConfig $routesConfig,
        private readonly ServicesConfig $servicesConfig,
    ) {
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [
            $this,
            $this->routesConfig,
            $this->servicesConfig,
        ];

        foreach ($this->formats as $format) {
            foreach ($format->getFiles() as $file) {
                $files[] = $file;
            }
        }

        foreach ($this->models as $model) {
            $files[] = $model;
        }

        foreach ($this->api->getFiles() as $file){
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @return array<string, Format>
     */
    private static function buildFormats(string $namespace, OpenApi $openApi): array
    {
        $formats = [];

        $addSchemaFormats = function (Reference|Schema $schema) use (&$addSchemaFormats, &$formats) {
            if ($schema instanceof Schema) {
                if ($schema->format !== null) {
                    $formats[$schema->format] = null;
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

        foreach ($formats as $rawName => &$format) {
            $format = Format::build($namespace, $rawName);
        }

        return $formats;
    }

    /**
     * @return array<Model>
     *
     * @throws Exception
     */
    private static function buildModels(string $namespace, OpenApi $openApi): array
    {
        $addModels = function(string $rawName, Reference|Schema $schema) use (&$addModels, &$models, $namespace, $openApi) {
            if ($schema instanceof Reference) {
                $schema = $openApi->components->schemas[$rawName = $schema->getName()];
            }
            if (!isset($models[$rawName])) {
                if ($schema->type === 'object') {
                    $models[$rawName] = Model::build(
                        $namespace,
                        "{$namespace}\Model",
                        "src/Model",
                        $rawName,
                        $schema,
                        $openApi->components,
                    );
                    foreach ($schema->properties as $propertyName => $property) {
                        $addModels("{$rawName}_{$propertyName}", $property);
                    }
                } elseif ($schema->type === 'array') {
                    $addModels($rawName, $schema->items);
                }
            }
        };

        foreach ($openApi->components->schemas as $rawName => $schema) {
            $addModels($rawName, $schema);
        }

        return $models;
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

    public function getClassName(): string
    {
        return "{$this->name}Bundle";
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getServiceName(): string
    {
        return u($this->name)->snake();
    }

    /**
     * @return array<Aggregate>
     */
    public function getAggregates(): array
    {
        return $this->api->getAggregates();
    }

    public function getFolder(): string
    {
        return 'src';
    }

    public function getName(): string
    {
        return "{$this->name}Bundle.php";
    }

    public function getTemplate(): string
    {
        return 'bundle.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'bundle';
    }
}