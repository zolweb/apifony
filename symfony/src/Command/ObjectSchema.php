<?php

namespace App\Command;

class ObjectSchema extends Schema
{
    public readonly ?array $properties;

    public function __construct(
        private readonly MediaType|Parameter|ObjectSchema|ArraySchema $context,
        private readonly ?string $schemaName,
        ?string $name,
        bool $required,
        array $data,
    ) {
        parent::__construct($name, $required);

        $this->properties = array_map(
            fn (string $name) => Schema::build(
                $this,
                $name,
                in_array($name, $data['required'] ?? [], true),
                $data['properties'][$name],
            ),
            array_keys($data['properties']),
        );
    }

    public function resolveReference(string $reference): array
    {
        return $this->context->resolveReference($reference);
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

    public function getConstraints(): array
    {
        $constraints = [
            new Constraint('Assert\Valid', []),
        ];

        if ($this->required) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        return $constraints;
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