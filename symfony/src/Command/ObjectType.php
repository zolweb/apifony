<?php

namespace App\Command;

class ObjectType implements Type
{
    public function __construct(
        private readonly Schema $schema,
    ) {
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
        return $this->schema->className;
    }

    public function getMethodParameterType(): string
    {
        return $this->schema->className;
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

    public function getContentInitializationFromRequest(): string
    {
        return "\$content = \$serializer->deserialize(\$request->getContent(), '{$this->schema->className}', JsonEncoder::FORMAT);";
    }

    public function getContentValidationViolationsInitialization(): string
    {
        return '$violations = $validator->validate($content);';
    }

    public function getNormalizedType(): string
    {
        return $this->schema->className;
    }

    public function getContentTypeChecking(): string
    {
        return "\$content instanceOf {$this->schema->className}";
    }

    public function getConstraints(): array
    {
        return [new Constraint('Assert\Valid', [])];
    }

    public function addFiles(array& $files): void
    {
        if (!isset($files[$this->schema->className])) {
            $files[$this->schema->className] = ['template' => 'schema.php.twig', 'params' => ['schema' => $this->schema]];

            foreach ($this->schema->properties as $property) {
                $property->addFiles($files);
            }
        }
    }

    public function __toString(): string
    {
        return 'object';
    }
}