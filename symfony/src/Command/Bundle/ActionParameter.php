<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Parameter;
use App\Command\OpenApi\Reference;
use function Symfony\Component\String\u;

class ActionParameter
{
    /**
     * @throws Exception
     */
    public static function build(
        string $actionClassName,
        Reference|Parameter $parameter,
        Components $components,
    ): self {
        if ($parameter instanceof Reference) {
            $parameter = $components->parameters[$parameter->getName()];
        }
        $variableName = sprintf('%s%s', $parameter->in[0], u($parameter->name)->camel());
        $className = "{$actionClassName}_{$parameter->name}";
        if ($parameter->schema instanceof Reference) {
            $parameter = $components->schemas[$className = $parameter->schema->getName()];
        }
        $className = u($className)->camel()->title();

        return new self(
            $variableName,
            $parameter,
            match ($parameter->schema->type) {
                'string' => new StringType($parameter->schema),
                'integer' => new IntegerType($parameter->schema),
                'number' => new NumberType($parameter->schema),
                'boolean' => new BooleanType($parameter->schema),
                'object' => new ObjectType($parameter->schema, $className, $components),
                'array' => new ArrayType($parameter->schema, $className, $components),
            },
        );
    }

    private function __construct(
        private readonly string $variableName,
        private readonly Parameter $parameter,
        private readonly Type $type,
    ) {
    }

    public function getIn(): string
    {
        return $this->parameter->in;
    }

    public function isArray(): bool
    {
        return $this->parameter->schema->type === 'array';
    }

    public function hasDefault(): bool
    {
        return $this->parameter->schema->default !== null;
    }

    public function getDefault(): string
    {
        return $this->type->getMethodParameterDefault();
    }

    public function getRawName(): string
    {
        return $this->parameter->name;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getConstraints(): array
    {
        return $this->type->getConstraints();
    }

    public function isNullable(): bool
    {
        return $this->parameter->schema->nullable;
    }

    public function getPhpType(): string
    {
        return $this->type->getMethodParameterType();
    }

    public function getPhpStanType(): string
    {
        return $this->type->getPhpDocParameterAnnotationType();
    }

    public function getCastFunction(): string
    {
        return $this->type->getStringToTypeCastFunction();
    }

    public function getRequestCollection(): string
    {
        return ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->parameter->in];
    }

    public function isRequired(): bool
    {
        return $this->parameter->required;
    }
}