<?php

namespace App\Command\OpenApi;

class Components
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $schemas = [];
        if (isset($data['schemas'])) {
            if (!is_array($data['schemas'])) {
                throw new Exception('Components schemas must be an array.');
            }
            foreach ($data['schemas'] as $name => $schemaData) {
                if (!is_string($name)) {
                    throw new Exception('Component schemas array keys must be strings');
                }
                if (!is_array($schemaData)) {
                    throw new Exception('Component schemas array elements must be strings');
                }

                $schemas[$name] = Schema::build($schemaData);
            }
        }

        $responses = [];
        if (isset($data['responses'])) {
            if (!is_array($data['responses'])) {
                throw new Exception('Components responses must be an array.');
            }
            foreach ($data['responses'] as $name => $responseData) {
                if (!is_string($name)) {
                    throw new Exception('Component responses array keys must be strings');
                }
                if (!is_array($responseData)) {
                    throw new Exception('Component responses array elements must be strings');
                }

                $responses[$name] = Response::build($responseData);
            }
        }

        $parameters = [];
        if (isset($data['parameters'])) {
            if (!is_array($data['parameters'])) {
                throw new Exception('Components parameters must be an array.');
            }
            foreach ($data['parameters'] as $name => $parameterData) {
                if (!is_string($name)) {
                    throw new Exception('Component parameters array keys must be strings');
                }
                if (!is_array($parameterData)) {
                    throw new Exception('Component parameters array elements must be strings');
                }

                $parameters[$name] = Parameter::build($parameterData);
            }
        }

        $requestBodies = [];
        if (isset($data['requestBodies'])) {
            if (!is_array($data['requestBodies'])) {
                throw new Exception('Components requestBodies must be an array.');
            }
            foreach ($data['requestBodies'] as $name => $requestBodyData) {
                if (!is_string($name)) {
                    throw new Exception('Component requestBodies array keys must be strings');
                }
                if (!is_array($requestBodyData)) {
                    throw new Exception('Component requestBodies array elements must be strings');
                }

                $requestBodies[$name] = RequestBody::build($requestBodyData);
            }
        }

        $headers = [];
        if (isset($data['headers'])) {
            if (!is_array($data['headers'])) {
                throw new Exception('Components headers must be an array.');
            }
            foreach ($data['headers'] as $name => $headerData) {
                if (!is_string($name)) {
                    throw new Exception('Component headers array keys must be strings');
                }
                if (!is_array($headerData)) {
                    throw new Exception('Component headers array elements must be strings');
                }

                $headers[$name] = Header::build($headerData);
            }
        }

        return new self($schemas, $responses, $parameters, $requestBodies, $headers);
    }

    /**
     *  @param array<string, Schema> $schemas
     *  @param array<string, Response> $responses
     *  @param array<string, Parameter> $parameters
     *  @param array<string, RequestBody> $requestBodies
     *  @param array<string, Header> $headers
     */
    private function __construct(
        public readonly array $schemas,
        public readonly array $responses,
        public readonly array $parameters,
        public readonly array $requestBodies,
        public readonly array $headers,
    ) {
    }
}