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
     * @throws Exception
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

        foreach ($openApi->components->schemas ?? [] as $schema) {
            $addSchemaFormats($schema);
        }
        foreach ($openApi->components->parameters ?? [] as $parameter) {
            if ($parameter->schema === null) {
                throw new Exception('Parameter objects without schema are not supported.');
            }
            $addSchemaFormats($parameter->schema);
        }
        foreach ($openApi->components->requestBodies ?? [] as $requestBody) {
            foreach ($requestBody->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('Mediatype objects without schema are not supported.');
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->responses ?? [] as $response) {
            foreach ($response->headers as $header) {
                if ($header instanceof Header) {
                    if ($header->schema === null) {
                        throw new Exception('Header objects without schema are not supported.');
                    }
                    $addSchemaFormats($header->schema);
                }
            }
            foreach ($response->content as $mediaType) {
                if ($mediaType->schema === null) {
                    throw new Exception('MediaType objects without schema are not supported.');
                }
                $addSchemaFormats($mediaType->schema);
            }
        }
        foreach ($openApi->components->headers ?? [] as $header) {
            if ($header->schema === null) {
                throw new Exception('Header objects without schema are not supported.');
            }
            $addSchemaFormats($header->schema);
        }
        foreach ($openApi?->paths->pathItems ?? [] as $pathItem) {
            foreach ($pathItem->parameters as $parameter) {
                if ($parameter instanceof Parameter) {
                    if ($parameter->schema === null) {
                        throw new Exception('Parameter objects without schema are not supported.');
                    }
                    $addSchemaFormats($parameter->schema);
                }
            }
            foreach ($pathItem->operations as $operation) {
                foreach ($operation->parameters as $parameter) {
                    if ($parameter instanceof Parameter) {
                        if ($parameter->schema === null) {
                            throw new Exception('Parameter objects without schema are not supported.');
                        }
                        $addSchemaFormats($parameter->schema);
                    }
                }
                if ($operation->requestBody instanceof RequestBody) {
                    foreach ($operation->requestBody->content as $mediaType) {
                        if ($mediaType->schema === null) {
                            throw new Exception('MediaType objects without schema are not supported.');
                        }
                        $addSchemaFormats($mediaType->schema);
                    }
                }
                foreach ($operation?->responses->responses ?? [] as $response) {
                    if ($response instanceof Response) {
                        foreach ($response->headers as $header) {
                            if ($header instanceof Header) {
                                if ($header->schema === null) {
                                    throw new Exception('Header objects without schema are not supported.');
                                }
                                $addSchemaFormats($header->schema);
                            }
                        }
                        foreach ($response->content as $mediaType) {
                            if ($mediaType->schema === null) {
                                throw new Exception('Mediatype objects without schema are not supported.');
                            }
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