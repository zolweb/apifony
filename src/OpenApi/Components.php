<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

class Components
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        $schemas = [];
        if (isset($data['schemas'])) {
            if (!\is_array($data['schemas'])) {
                throw new Exception('Components schemas must be an array.', $path);
            }
            foreach ($data['schemas'] as $name => $schemaData) {
                if (!\is_string($name)) {
                    throw new Exception('Component schemas array keys must be strings', $path);
                }
                if (!\is_array($schemaData)) {
                    throw new Exception('Component schemas array elements must be arrays', $path);
                }
                $schemaPath = $path;
                $schemaPath[] = 'schemas';
                $schemaPath[] = $name;
                $schemas[$name] = Schema::build($schemaData, $schemaPath);
            }
        }

        $responses = [];
        if (isset($data['responses'])) {
            if (!\is_array($data['responses'])) {
                throw new Exception('Components responses must be an array.', $path);
            }
            foreach ($data['responses'] as $name => $responseData) {
                if (!\is_string($name)) {
                    throw new Exception('Component responses array keys must be strings', $path);
                }
                if (!\is_array($responseData)) {
                    throw new Exception('Component responses array elements must be arrays', $path);
                }
                $responsePath = $path;
                $responsePath[] = 'responses';
                $responsePath[] = $name;
                $responses[$name] = Response::build($responseData, $responsePath);
            }
        }

        $parameters = [];
        if (isset($data['parameters'])) {
            if (!\is_array($data['parameters'])) {
                throw new Exception('Components parameters must be an array.', $path);
            }
            foreach ($data['parameters'] as $name => $parameterData) {
                if (!\is_string($name)) {
                    throw new Exception('Component parameters array keys must be strings', $path);
                }
                if (!\is_array($parameterData)) {
                    throw new Exception('Component parameters array elements must be arrays', $path);
                }
                $parameterPath = $path;
                $parameterPath[] = 'parameters';
                $parameterPath[] = $name;
                $parameters[$name] = Parameter::build($parameterData, $parameterPath);
            }
        }

        $requestBodies = [];
        if (isset($data['requestBodies'])) {
            if (!\is_array($data['requestBodies'])) {
                throw new Exception('Components requestBodies must be an array.', $path);
            }
            foreach ($data['requestBodies'] as $name => $requestBodyData) {
                if (!\is_string($name)) {
                    throw new Exception('Component requestBodies array keys must be strings', $path);
                }
                if (!\is_array($requestBodyData)) {
                    throw new Exception('Component requestBodies array elements must be arrays', $path);
                }
                $requestBodyPath = $path;
                $requestBodyPath[] = 'requestBodies';
                $requestBodyPath[] = $name;
                $requestBodies[$name] = RequestBody::build($requestBodyData, $requestBodyPath);
            }
        }

        $headers = [];
        if (isset($data['headers'])) {
            if (!\is_array($data['headers'])) {
                throw new Exception('Components headers must be an array.', $path);
            }
            foreach ($data['headers'] as $name => $headerData) {
                if (!\is_string($name)) {
                    throw new Exception('Component headers array keys must be strings', $path);
                }
                if (!\is_array($headerData)) {
                    throw new Exception('Component headers array elements must be arrays', $path);
                }
                $headerPath = $path;
                $headerPath[] = 'header';
                $headerPath[] = $name;
                $headers[$name] = Header::build($headerData, $headerPath);
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self($schemas, $responses, $parameters, $requestBodies, $headers, $extensions, $path);
    }

    /**
     * @param array<string, Schema>      $schemas
     * @param array<string, Response>    $responses
     * @param array<string, Parameter>   $parameters
     * @param array<string, RequestBody> $requestBodies
     * @param array<string, Header>      $headers
     * @param array<string, mixed>       $extensions
     * @param list<string>               $path
     */
    private function __construct(
        public readonly array $schemas,
        public readonly array $responses,
        public readonly array $parameters,
        public readonly array $requestBodies,
        public readonly array $headers,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
