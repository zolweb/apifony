<?php

namespace App\Command\OpenApi;

class Parameter
{
    public readonly string $className;
    public readonly string $name;
    public readonly string $in;
    public readonly bool $required;
    public readonly Schema $schema;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array& $components, array $data): self
    {
        $parameter = new self();

        if (isset($data['$ref'])) {
            $className = explode('/', $data['$ref'])[3];
            $component = &$components['parameters'][$className];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $parameter;
                $data = $component['data'];
            }
        }

        $parameter->className = $className;
        $parameter->name = $data['name'];
        $parameter->in = $data['in'];
        $parameter->required = $data['required'] ?? false;
        $parameter->schema = Schema::build("{$className}Schema", $components, $data['schema']);

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