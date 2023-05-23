<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;

class Me
{
    public readonly Type $type;
    public readonly \App\Command\OpenApi\Parameter $parameter;

    public static function build(
        \App\Command\OpenApi\Parameter $parameter,
        Components $components,
    ): self {
        $self = new self();
        $self->parameter = $parameter;
        $self->type = match ($parameter->schema->type) {
            'string' => new StringType($parameter->schema),
            'integer' => new IntegerType($parameter->schema),
            'number' => new NumberType($parameter->schema),
            'boolean' => new BooleanType($parameter->schema),
            'object' => new ObjectType($parameter->schema, 'Cool', $components),
            'array' => new ArrayType($parameter->schema, $components),
        };

        return $self;
    }

    private function __construct()
    {
    }

    public function getVariableName(): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$this->parameter->in],
            ucfirst($this->parameter->name),
        );
    }

    public function getRequestCollection(): string
    {
        return ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->parameter->in];
    }
}