<?php

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Reference;
use Zol\Ogen\OpenApi\Schema;
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
        ?Components $components,
    ): self {
        $usedModelName = null;
        $variableName = u($rawName)->camel();
        $className = "{$modelClassName}_{$variableName}";
        if ($property instanceof Reference) {
            if ($components === null || !isset($components->schemas[$property->getName()])) {
                throw new Exception('Reference not found in schemas components.');
            }
            $property = $components->schemas[$className = $usedModelName = $property->getName()];
        }
        $className = u($className)->camel()->title();
        $type = TypeFactory::build($className, $property, $components);
        if ($property->type === 'array') {
            $usedModelName = $type->getUsedModel();
        }

        return new self(
            $rawName,
            $variableName,
            $property,
            $type,
            $usedModelName,
        );
    }

    private function __construct(
        private readonly string $rawName,
        private readonly string $variableName,
        private readonly Schema $schema,
        private readonly Type $type,
        private readonly ?string $usedModelName,
    ) {
    }

    public function getUsedModelName(): ?string
    {
        return $this->usedModelName;
    }

    public function isArray(): bool
    {
        return $this->schema->type === 'array';
    }

    public function hasDefault(): bool
    {
        return $this->schema->default !== null;
    }

    public function getDefault(): ?string
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