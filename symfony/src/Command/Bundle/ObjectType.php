<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Schema;

class ObjectType implements Type
{
    public function __construct(
        private readonly Schema $schema,
        private readonly string $name,
    ) {
    }

    public function getArrayProperties(): array
    {
        return array_filter(
            $this->schema->properties,
            static fn (Schema $property) => (string)$property->type === 'array',
        );
    }

    public function getSortedProperties(): array
    {
        $propertiesWithoutDefault = array_filter(
            $this->schema->properties,
            static fn (Schema $property) => $property->default === null,
        );

        $propertiesWithDefault = array_filter(
            $this->schema->properties,
            static fn (Schema $property) => $property->default !== null,
        );

        return array_merge($propertiesWithoutDefault, $propertiesWithDefault);
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return $this->name;
    }

    public function getMethodParameterType(): string
    {
        return $this->name;
    }

    public function getMethodParameterDefault(): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(): string
    {
        throw new Exception('Object path parameters are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        throw new Exception('Object parameters are not supported.');
    }

    public function getRequestBodyPayloadInitializationFromRequest(): string
    {
        return "\$requestBodyPayload = \$serializer->deserialize(\$request->getContent(), '{$this->name}', JsonEncoder::FORMAT);";
    }

    public function getRequestBodyPayloadValidationViolationsInitialization(): string
    {
        return '$violations = $this->validator->validate($requestBodyPayload);';
    }

    public function getNormalizedType(): string
    {
        return $this->name;
    }

    public function getRequestBodyPayloadTypeChecking(): string
    {
        return "\$requestBodyPayload instanceOf {$this->name}";
    }

    public function getConstraints(): array
    {
        return [new Constraint('Assert\Valid', [])];
    }

    public function addFiles(array& $files, string $folder): void
    {
        $folder = $this->schema->isComponent ? 'src/Schema' : $folder;

        if (!isset($files["{$folder}/{$this->name}.php"])) {
            $files["{$folder}/{$this->name}.php"] = [
                'folder' => $folder,
                'name' => "{$this->name}.php",
                'template' => 'schema.php.twig',
                'params' => [
                    'schema' => $this->schema,
                ],
            ];

            foreach ($this->schema->properties as $property) {
                $property->addFiles($files, $folder);
            }
        }
    }

    public function __toString(): string
    {
        return 'object';
    }
}