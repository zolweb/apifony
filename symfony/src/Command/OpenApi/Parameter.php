<?php

namespace App\Command\OpenApi;

class Parameter
{
    public readonly string $className;
    public readonly string $name;
    public readonly string $in;
    public readonly bool $required;
    public readonly Reference|Schema $schema;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array $data): self
    {
        $parameter = new self();
        $parameter->className = $className;
        $parameter->name = $data['name'];
        $parameter->in = $data['in'];
        $parameter->required = $data['required'] ?? false;
        $parameter->schema = isset($data['schema']['$ref']) ?
            Reference::build($data['schema']) :
            Schema::build("{$className}Schema", $data['schema']);

        return $parameter;
    }

    private function __construct()
    {
    }

    public function getVariableName(): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$this->in],
            ucfirst($this->name),
        );
    }

    public function getRequestCollection(): string
    {
        return ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->in];
    }
}