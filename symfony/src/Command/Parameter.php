<?php

namespace App\Command;

class Parameter
{
    public readonly string $className;
    public readonly string $in;
    public readonly string $name;
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
        $parameter->in = $data['in'];
        $parameter->name = $data['name'];
        $parameter->schema = Schema::build("{$className}Schema", $components, $data['schema']);

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