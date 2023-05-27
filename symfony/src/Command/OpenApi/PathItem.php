<?php

namespace App\Command\OpenApi;

class PathItem
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data, ?Components $components): self
    {
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
        $extensions = [];
        foreach ($data as $key => $elementData) {
            if (in_array($key, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true)) {
                if (!is_array($elementData)) {
                    throw new Exception('Operation object array elements must be objects.');
                }
                $operations[$key] = Operation::build($parameters, $elementData, $components);
            } elseif (is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $elementData;
            }
        }

        return new self($parameters, $operations, $extensions);
    }

    /**
     * @param array<Reference|Parameter> $parameters
     * @param array<string, Operation> $operations
     * @param array<string, mixed> $extensions
     */
    private function __construct(
        public readonly array $parameters,
        public readonly array $operations,
        public readonly array $extensions,
    ) {
    }
}