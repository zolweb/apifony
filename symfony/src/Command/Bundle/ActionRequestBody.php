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
        Components $components,
    ): self {
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
                $schema = $components->schemas[$schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);

            if ($hasModel) {
                $addModels = function(string $rawName, Reference|Schema $schema) use (&$addModels, &$payloadModels, $bundleNamespace, $aggregateName, $className, $components) {
                    if (!$schema instanceof Reference) {
                        if ($schema->type === 'object') {
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
                        } elseif ($schema->type === 'array') {
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

    public function getPayloadNormalizedType(): string
    {
        return $this->payloadType?->getNormalizedType() ?? 'Empty';
    }

    public function getPayloadPhpType(): string
    {
        return $this->payloadType->getPhpDocParameterAnnotationType();
    }

    public function initializationFromRequest(): string
    {
        return $this->payloadType->getRequestBodyPayloadInitializationFromRequest();
    }

    public function validationViolationsInitialization(): string
    {
        return $this->payloadType->getRequestBodyPayloadValidationViolationsInitialization();
    }

    /**
     * @return array<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }
}