<?php

namespace App\Command;

class Parameter
{
    public readonly Operation|PathItem $parent;
    public readonly string $in;
    public readonly string $name;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(Operation|PathItem $parent, array $componentsData, array $data): self
    {
        $parameter = new self();
        $parameter->parent = $parent;
        $parameter->in = $data['in'];
        $parameter->name = $data['name'];
        $parameter->schema = Schema::build($parameter, $parameter->toVariableName(), $componentsData, $data['schema']);

        if (in_array($data['schema']['type'] ?? '', ['array', 'object'], true)) {
            throw new Exception("Parameters of {$data['schema']['type']} type are not supported yet.");
        }


        return $parameter;
    }

    public function hasDefault(): bool
    {
        return $this->schema->getMethodParameterDefault() !== null;
    }

    public function toVariableName(): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$this->in],
            ucfirst($this->name),
        );
    }

    public function getRouteRequirement(): string
    {
        return $this->schema->getRouteRequirement();
    }

    public function getMethodParameter(): string
    {
        return $this->schema->getMethodParameter();
    }

    public function getInitializationFromRequest(): string
    {
        return sprintf(
            '$%s = %s($request->%s->get(\'%s\'%s));',
            $this->toVariableName(),
            $this->schema->getStringToTypeCastFunction(),
            ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->in],
            $this->name,
            $this->schema->getMethodParameterDefault() !== null ?
                sprintf(
                    ', %s',
                    $this->schema->getMethodParameterDefault(),
                ) : '',
        );
    }

    public function getConstraints(): array
    {
        return $this->schema->getConstraints();
    }
}