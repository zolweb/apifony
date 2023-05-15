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
        if (isset($data['$ref'])) {
            $data = $componentsData['parameters'][explode('/', $data['$ref'])[3]];
        }

        $parameter = new self();
        $parameter->parent = $parent;
        $parameter->in = $data['in'];
        $parameter->name = $data['name'];
        $parameter->schema = Schema::build($parameter, $componentsData, $data['schema']);

        return $parameter;
    }

    private function __construct()
    {
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