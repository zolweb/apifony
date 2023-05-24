<?php

namespace App\Command\OpenApi;

class Components
{
    /**
     * @throws Exception
     */
    public static function build(mixed $data): self
    {
        if (!is_array($data)) {
            throw new Exception('Components object must be an array.');
        }

        $schemas = [];
        foreach ($data['schemas'] ?? [] as $name => $schemaData) {
            if (!is_string($name)) {
                throw new Exception('Component schemas array keys must be strings');
            }

            $schemas[$name] = Schema::build($schemaData);
        }

        $responses = [];
        foreach ($data['responses'] ?? [] as $name => $responseData) {
            if (!is_string($name)) {
                throw new Exception('Component responses array keys must be strings');
            }

            $responses[$name] = Response::build($responseData);
        }

        $parameters = [];
        foreach ($data['parameters'] ?? [] as $name => $parameterData) {
            if (!is_string($name)) {
                throw new Exception('Component parameters array keys must be strings');
            }

            $parameters[$name] = Parameter::build($parameterData);
        }

        $requestBodies = [];
        foreach ($data['requestBodies'] ?? [] as $name => $requestBodyData) {
            if (!is_string($name)) {
                throw new Exception('Component requestBodies array keys must be strings');
            }

            $requestBodies[$name] = RequestBody::build($requestBodyData);
        }

        $headers = [];
        foreach ($data['headers'] ?? [] as $name => $headerData) {
            if (!is_string($name)) {
                throw new Exception('Component headers array keys must be strings');
            }

            $headers[$name] = Header::build($headerData);
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