<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\MediaType;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class ActionRequestBody
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        string $actionName,
        ?string $mimeType,
        ?MediaType $mediaType,
        ?Components $components,
    ): self {
        if (!in_array($mimeType, [null, 'application/json'], true)) {
            throw new Exception('Request bodies with mime types other than \'application/json\' are not supported.');
        }

        $className = u(sprintf('%s_%s_RequestBodyPayload', $actionName, $mimeType ?? 'empty'))->camel()->title();

        $payloadModels = [];
        $payloadType = null;
        if ($mediaType !== null) {
            if ($mediaType->schema === null) {
                throw new Exception('Mediatypes without schema are not supported.');
            }
            $schema = $mediaType->schema;
            $hasModel = true;
            if ($schema instanceof Reference) {
                if ($components === null || !isset($components->schemas[$schema->getName()])) {
                    throw new Exception('Reference not found in schemas components.');
                }
                $schema = $components->schemas[$schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);

            if ($hasModel) {
                $addModels = function(string $rawName, Reference|Schema $schema) use (&$addModels, &$payloadModels, $bundleNamespace, $aggregateName, $className, $components) {
                    if (!$schema instanceof Reference) {
                        $type = TypeFactory::build('', $schema, $components);
                        if ($type instanceof ObjectType) {
                            $payloadModels[$rawName] = Model::build(
                                $bundleNamespace,
                                "{$bundleNamespace}\Api\\{$aggregateName}",
                                "src/Api/{$aggregateName}",
                                $className,
                                $schema,
                                $components,
                            );
                            foreach ($schema->properties as $propertyName => $property) {
                                $addModels("{$rawName}_{$propertyName}", $property);
                            }
                        } elseif ($type instanceof ArrayType) {
                            if ($schema->items === null) {
                                throw new Exception('Schema objects of array type without items attribute are not supported.');
                            }
                            $addModels($rawName, $schema->items);
                        }
                    }
                };

                $addModels($className, $schema);
            }
        }

        return new self(
            $mimeType,
            $payloadType,
            $payloadModels,
        );
    }

    /**
     * @param array<Model> $payloadModels
     */
    private function __construct(
        private readonly ?string $mimeType,
        private readonly ?Type $payloadType,
        private readonly array $payloadModels,
    ) {
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getPayloadType(): ?Type
    {
        return $this->payloadType;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->payloadType?->getConstraints() ?? [];
    }

    public function getPayloadNormalizedType(): string
    {
        return $this->payloadType?->getNormalizedType() ?? 'Empty';
    }

    public function getPayloadOpenApiType(): ?string
    {
        return $this->payloadType?->getMethodParameterType();
    }

    public function getPayloadPhpType(): ?string
    {
        return $this->payloadType?->getMethodParameterType();
    }

    public function getPayloadBuiltInPhpType(): ?string
    {
        return $this->payloadType?->getBuiltInPhpType();
    }

    public function initializationFromRequest(): ?string
    {
        return $this->payloadType?->getRequestBodyPayloadInitializationFromRequest();
    }

    public function validationViolationsInitialization(): ?string
    {
        return $this->payloadType?->getRequestBodyPayloadValidationViolationsInitialization();
    }

    /**
     * @return array<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }
}