<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class ObjectType implements Type
{
    public function __construct(
        private readonly Schema $schema,
        private readonly string $name,
        private readonly Components $components,
    ) {
    }

    public function getArrayProperties(): array
    {
        return array_filter(
            $this->schema->properties,
            fn (Reference|Schema $property) => (
                $property instanceof Reference ?
                    $this->components->schemas[$property->getName()]->type :
                    $property->type
            ) === 'array',
        );
    }

    public function getSortedProperties(): array
    {
        $propertiesWithoutDefault = array_filter(
            $this->schema->properties,
            fn (Reference|Schema $property) => (
                $property instanceof Reference ?
                    $this->components->schemas[$property->getName()]->default :
                    $property->default
            ) === null,
        );

        $propertiesWithDefault = array_filter(
            $this->schema->properties,
            fn (Reference|Schema $property) => (
                $property instanceof Reference ?
                    $this->components->schemas[$property->getName()]->default :
                    $property->default
            ) !== null,
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
        return "\$requestBodyPayload = \$this->serializer->deserialize(\$request->getContent(), '{$this->name}', JsonEncoder::FORMAT);";
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
        $constraints = [new Constraint('Assert\Valid', [])];

        if (!$this->schema->nullable) {
            $constraints[] = new Constraint('Assert\NotNull', []);
        }

        if ($this->schema->format !== null) {
            $constraints[] = new Constraint(sprintf('Assert%s', u($this->schema->format)->camel()->title()), [], $this->schema->format);
        }

        return $constraints;
    }
}