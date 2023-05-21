<?php

namespace App\Command\OpenApi;

class Components
{
    /** @var array<Schema> */
    public readonly array $schemas;
    /** @var array<Response> */
    public readonly array $responses;
    /** @var array<Parameter> */
    public readonly array $parameters;
    /** @var array<RequestBody> */
    public readonly array $requestBodies;
    /** @var array<Header> */
    public readonly array $headers;

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $self = new self();
        $self->schemas = array_combine(
            $names = array_keys($data['schemas'] ?? []),
            array_map(
                static fn (string $name) => Schema::build($name, $data['schemas'][$name]),
                $names,
            )
        );
        $self->responses = array_combine(
            $names = array_keys($data['responses'] ?? []),
            array_map(
                static fn (string $name) => Response::build($name, $data['responses'][$name]),
                $names,
            )
        );
        $self->parameters = array_combine(
            $names = array_keys($data['parameters'] ?? []),
            array_map(
                static fn (string $name) => Parameter::build($name, $data['parameters'][$name]),
                $names,
            )
        );
        $self->requestBodies = array_combine(
            $names = array_keys($data['requestBodies'] ?? []),
            array_map(
                static fn (string $name) => RequestBody::build($name, $data['requestBodies'][$name]),
                $names,
            )
        );
        $self->headers = array_combine(
            $names = array_keys($data['headers'] ?? []),
            array_map(
                static fn (string $name) => Header::build($name, $data['headers'][$name]),
                $names,
            )
        );

        return $self;
    }
}