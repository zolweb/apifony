<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Reference;
use App\Command\OpenApi\Schema;
use function Symfony\Component\String\u;

class ModelAttribute
{
    /**
     * @throws Exception
     */
    public static function build(
        string $modelClassName,
        string $rawName,
        Reference|Schema $property,
        Components $components,
    ): self {
        $variableName = u($rawName)->camel();
        $className = "{$modelClassName}_{$variableName}";
        if ($property instanceof Reference) {
            $property = $components->schemas[$className = $property->getName()];
        }
        $className = u($className)->camel()->title();

        return new self(
            $rawName,
            $variableName,
            $property,
            TypeFactory::build($className, $property, $components),
        );
    }

    private function __construct(
        private readonly string $rawName,
        private readonly string $variableName,
        private readonly Schema $schema,
        private readonly Type $type,
    ) {
    }

    public function isArray(): bool
    {
        return $this->schema->type === 'array';
    }

    public function hasDefault(): bool
    {
        return $this->schema->default !== null;
    }

    public function getDefault(): string
    {
        return $this->type->getMethodParameterDefault();
    }

    public function getRawName(): string
    {
        return $this->rawName;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function isNullable(): bool
    {
        return $this->type->isNullable();
    }

    public function getPhpType(): string
    {
        return $this->type->getMethodParameterType();
    }

    public function getPhpStanType(): string
    {
        return $this->type->getPhpDocParameterAnnotationType();
    }
}