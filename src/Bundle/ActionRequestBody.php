<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\MediaType;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;

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
        if (!\in_array($mimeType, [null, 'application/json'], true)) {
            throw new Exception('Request bodies with mime types other than \'application/json\' are not supported.');
        }

        $className = u(sprintf('%s_%s_RequestBodyPayload', $actionName, $mimeType ?? 'empty'))->camel()->title()->toString();

        $payloadModels = [];
        $payloadType = null;
        $usedModelName = null;
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
                $schema = $components->schemas[$className = $usedModelName = $schema->getName()];
                $hasModel = false;
            }
            $payloadType = TypeFactory::build($className, $schema, $components);
            if ($payloadType instanceof ArrayType) {
                throw new Exception('Request bodies with array schema are not supported.');
            }

            if ($hasModel) {
                $addModels = static function (string $rawName, Reference|Schema $schema) use (&$addModels, &$payloadModels, $bundleNamespace, $aggregateName, $components): void {
                    if (!$schema instanceof Reference) {
                        $type = TypeFactory::build('', $schema, $components);
                        if ($type instanceof ObjectType) {
                            if (!($schema->extensions['x-raw'] ?? false)) {
                                $payloadModels[$rawName] = Model::build(
                                    $bundleNamespace,
                                    "{$bundleNamespace}\\Api\\{$aggregateName}",
                                    "src/Api/{$aggregateName}",
                                    $rawName,
                                    $schema,
                                    $components,
                                    false,
                                );
                                foreach ($schema->properties as $propertyName => $property) {
                                    $addModels("{$rawName}_{$propertyName}", $property);
                                }
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
            $usedModelName,
        );
    }

    /**
     * @param array<Model> $payloadModels
     */
    private function __construct(
        private readonly ?string $mimeType,
        private readonly ?Type $payloadType,
        private readonly array $payloadModels,
        private readonly ?string $usedModelName,
    ) {
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
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

    public function getPayloadPhpType(): string
    {
        $type = $this->payloadType?->getMethodParameterType();

        if ($type === null) {
            throw new \RuntimeException();
        }

        return $type;
    }

    public function getPayloadBuiltInPhpType(): string
    {
        $type = $this->payloadType?->getBuiltInPhpType();

        if ($type === null) {
            throw new \RuntimeException();
        }

        return $type;
    }

    /**
     * @return array<Model>
     */
    public function getPayloadModels(): array
    {
        return $this->payloadModels;
    }
}
