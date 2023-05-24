<?php

namespace App\Command\OpenApi;

class PathItem
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
    {
        if (!is_array($data)) {
            throw new Exception('PathItem objects must be arrays.');
        }

        $parameters = [];
        if (isset($data['parameters'])) {
            if (!is_array($data['parameters'])) {
                throw new Exception('PathItem parameters must be an array.');
            }
            foreach ($data['parameters'] as $parameterData) {
                if (!is_array($parameterData)) {
                    throw new Exception('Parameter or Reference objects must be arrays.');
                }
                $parameters[] = isset($parameterData['$ref']) ?
                    Reference::build($parameterData) :
                    Parameter::build($parameterData);
            }
        }

        $operations = [];
        foreach (['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'] as $method) {
            if (isset($data[$method])) {
                $operations[$method] = Operation::build($parameters, $data[$method]);
            }
        }

        return new self($parameters, $operations);
    }

    /**
     * @param array<Reference|Parameter> $parameters
     * @param array<string, Operation> $operations
     */
    private function __construct(
        public readonly array $parameters,
        public readonly array $operations,
    ) {
    }
}