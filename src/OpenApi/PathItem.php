<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class PathItem
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, ?Components $components, array $path): self
    {
        $parameters = [];
        if (isset($data['parameters'])) {
            if (!\is_array($data['parameters'])) {
                throw new Exception('PathItem parameters must be an array.', $path);
            }
            foreach ($data['parameters'] as $parameterIndex => $parameterData) {
                if (!\is_int($parameterIndex)) {
                    throw new Exception('Parameter indexes must be integers.', $path);
                }
                if (!\is_array($parameterData)) {
                    throw new Exception('Parameter or Reference objects must be arrays.', $path);
                }
                $parameterPath = $path;
                $parameterPath[] = 'parameters';
                $parameterPath[] = (string) $parameterIndex;
                $parameters[] = isset($parameterData['$ref']) ?
                    Reference::build($parameterData, $parameterPath) :
                    Parameter::build($parameterData, $parameterPath);
            }
        }

        $operations = [];
        $extensions = [];
        foreach ($data as $key => $elementData) {
            if (\in_array($key, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true)) {
                if (!\is_array($elementData)) {
                    throw new Exception('Operation object array elements must be objects.', $path);
                }
                $operationPath = $path;
                $operationPath[] = $key;
                $operations[$key] = Operation::build($parameters, $elementData, $components, $operationPath);
            } elseif (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $elementData;
            }
        }

        return new self($parameters, $operations, $extensions, $path);
    }

    /**
     * @param list<Reference|Parameter> $parameters
     * @param array<string, Operation>  $operations
     * @param array<string, mixed>      $extensions
     * @param list<string>              $path
     */
    private function __construct(
        public readonly array $parameters,
        public readonly array $operations,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
