<?php

namespace App\Command;

class ObjectSchema implements SchemaType
{
    public readonly ?array $properties;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly MediaType|Parameter|ObjectSchema|ArraySchema $context,
        private readonly ?string $schemaName,
        private readonly ?string $name,
        array $data,
    ) {
        $this->properties = array_map(
            fn (string $name) => new Schema(
                $context,
                $name,
                in_array($name, $data['required'] ?? [], true),
                $data['properties'][$name],
            ),
            array_keys($data['properties']),
        );
    }

    public function getClassName(): string
    {
        return $this->schemaName ?? sprintf('%s%s', $this->context->getClassName(), ucfirst($this->name ?? ''));
    }

    public function getArrayProperties(): array
    {
        return array_filter(
            $this->properties,
            static fn (Schema $property) => $property instanceof ArraySchema,
        );
    }

    public function getPhpDocParameterAnnotationType(): string
    {
        return $this->getClassName();
    }

    public function getMethodParameterType(): string
    {
        return $this->getClassName();
    }

    public function getMethodParameterDefault(): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirement(): string
    {
        throw new Exception('Object parameters in path are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(): string
    {
        throw new Exception('Object parameters are not supported.');
    }

    public function getContentInitializationFromRequest(): string
    {
        return "\$content = \$serializer->deserialize(\$request->getContent(), '{$this->getClassName()}', JsonEncoder::FORMAT);";
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return '$violations = $validator->validate($content);';
    }

    public function getNormalizedType(): string
    {
        return $this->getClassName();
    }

    public function getContentTypeChecking(): string
    {
        return "\$content instanceOf {$this->getClassName()}";
    }

    public function getConstraints(): array
    {
        return [new Constraint('Assert\Valid', [])];
    }

    public function getFiles(): array
    {
        return array_merge(
            [$this->getClassName() => ['template' => 'schema.php.twig', 'params' => ['schema' => $this]]],
            ...array_map(
                static fn (Schema $property) => $property->getFiles(),
                $this->properties,
            ),
        );
    }
}