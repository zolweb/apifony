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
        foreach ($data['schemas'] ?? [] as $name => $schema) {
            if (!is_string($name)) {
                throw new Exception('Component schemas names must be strings');
            }

            $schemas[$name] = Schema::build($schema);
        }

        $responses = [];
        foreach ($data['responses'] ?? [] as $name => $response) {
            if (!is_string($name)) {
                throw new Exception('Component responses names must be strings');
            }

            $responses[$name] = Response::build($response);
        }

        $parameters = [];
        foreach ($data['parameters'] ?? [] as $name => $parameter) {
            if (!is_string($name)) {
                throw new Exception('Component parameters names must be strings');
            }

            $parameters[$name] = Parameter::build($parameter);
        }

        $requestBodies = [];
        foreach ($data['requestBodies'] ?? [] as $name => $requestBody) {
            if (!is_string($name)) {
                throw new Exception('Component requestBodies names must be strings');
            }

            $requestBodies[$name] = RequestBody::build($requestBody);
        }

        $headers = [];
        foreach ($data['headers'] ?? [] as $name => $header) {
            if (!is_string($name)) {
                throw new Exception('Component headers names must be strings');
            }

            $headers[$name] = Header::build($header);
        }

        return new self(
            $schemas,
            $responses,
            $parameters,
            $requestBodies,
            $headers,
        );
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