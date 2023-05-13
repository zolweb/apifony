<?php

namespace App\Command;

class Parameter
{
    public readonly string $in;
    public readonly string $name;
    private readonly Schema $schema;
    private readonly bool $required;

    public function __construct(
        array $data,
    ) {
        if (($data['schema']['type'] ?? '') === 'array') {
            throw new \RuntimeException('Parameters of array type are not supported yet.');
        }

        $this->in = $data['in'];
        $this->name = $data['name'];
        $this->schema = new Schema($this->name, $data['required'] ?? false, $data['schema']);
        $this->required = $data['required'] ?? false;
    }

    public function hasDefault(): bool
    {
        return $this->schema->default !== null;
    }

    public function toVariableName(): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$this->in],
            ucfirst($this->name),
        );
    }

    public function toRouteRequirement(): string
    {
        return sprintf(
            '\'%s\' => \'%s\',',
            $this->toVariableName(),
            match ($param['schema']['type'] ?? 'mixed') {
                // TODO https://datatracker.ietf.org/doc/html/draft-bhutton-json-schema-validation-00#section-7.3
                'string' => $param['schema']['pattern'] ?? '[^:/?#[]@!$&\\\'()*+,;=]+',
                'number' => '-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?',
                'integer' => '-?(0|[1-9]\d*)',
                'boolean' => 'true|false',
                default => '[^:/?#[]@!$&\\\'()*+,;=]+',
            }
        );
    }

    public function toMethodParameter(): string
    {
        return sprintf(
            '%s%s $%s%s,',
            ($param['required'] ?? false) ? '' : '?',
            isset($param['schema']['type']) ?
                ['string' => 'string', 'number' => 'float', 'integer' => 'int', 'boolean' => 'bool', 'array' => 'array'][$param['schema']['type']] :
                'mixed',
            $this->toVariableName(),
            ($default = $this->schema->getDefaultAsMethodParameterDefault()) !== null ? sprintf(' = %s', $default) : '',
        );
    }

    public function toFromRequestVariableInitialization(): string
    {
        return sprintf(
            '$%s = %s($request->%s->get(\'%s\', %s));',
            $this->toVariableName(),
            ['number' => 'floatval', 'integer' => 'intval', 'boolean' => 'boolval'][$this->schema->type] ?? '',
            ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->in],
            $this->name,
            $this->schema->default ?? 'null',
        );
    }

    public function getConstraints(): array
    {
        // $constraints = [];

        // if ($required) {
        //     $constraints[] = 'Assert\NotNull()';
        // }

        return $this->schema->getConstraints();
    }
}