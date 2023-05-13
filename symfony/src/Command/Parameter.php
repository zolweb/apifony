<?php

namespace App\Command;

use Exception;

class Parameter
{
    public readonly string $in;
    public readonly string $name;
    private readonly Schema $schema;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly Operation|Path $parent,
        array $data,
    ) {
        if (in_array($data['schema']['type'] ?? '', ['array', 'object'], true)) {
            throw new Exception("Parameters of {$data['schema']['type']} type are not supported yet.");
        }

        $this->in = $data['in'];
        $this->name = $data['name'];
        $this->schema = Schema::build($this, $this->toVariableName(), $data['required'] ?? false, $data['schema']);
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

    public function resolveReference(string $reference): array
    {
        return $this->parent->resolveReference($reference);
    }
}