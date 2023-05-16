<?php

namespace App\Command;

class ObjectType implements Type
{
    public function getArrayProperties(): array
    {
        return array_filter(
            $this->properties,
            static fn (Schema $property) => $property instanceof ArraySchema,
        );
    }

    public function getPhpDocParameterAnnotationType(Schema $schema): string
    {
        return $schema->className;
    }

    public function getMethodParameterType(Schema $schema): string
    {
        return $schema->className;
    }

    public function getMethodParameterDefault(Schema $schema): ?string
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function getRouteRequirementPattern(Schema $schema): string
    {
        throw new Exception('Object path parameters are not supported.');
    }

    /**
     * @throws Exception
     */
    public function getStringToTypeCastFunction(Schema $schema): string
    {
        throw new Exception('Object parameters are not supported.');
    }

    public function getContentInitializationFromRequest(Schema $schema): string
    {
        return "\$content = \$serializer->deserialize(\$request->getContent(), '{$schema->className}', JsonEncoder::FORMAT);";
    }

    public function getContentValidationViolationsInitialization(Schema $schema): string
    {
        return '$violations = $validator->validate($content);';
    }

    public function getNormalizedType(Schema $schema): string
    {
        return $schema->className;
    }

    public function getContentTypeChecking(Schema $schema): string
    {
        return "\$content instanceOf {$schema->className}";
    }

    public function getConstraints(Schema $schema): array
    {
        return [new Constraint('Assert\Valid', [])];
    }

    public function getFiles(Schema $schema): array
    {
        static $schemas = [];

        if (isset($schemas[$schema->className]))
        return array_merge(
            [$schema->className => ['template' => 'schema.php.twig', 'params' => ['schema' => $this]]],
            ...array_map(
                static fn (Schema $property) => $property->getFiles(),
                $schema->properties,
            ),
        );
    }

    public function __toString(): string
    {
        return 'object';
    }
}