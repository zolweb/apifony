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
        return new self(
            array_combine(
                $names = array_keys($data['schemas'] ?? []),
                array_map(
                    static fn (string $name) => Schema::build($data['schemas'][$name]),
                    $names,
                )
            ),
            array_combine(
                $names = array_keys($data['responses'] ?? []),
                array_map(
                    static fn (string $name) => Response::build($data['responses'][$name]),
                    $names,
                )
            ),
            array_combine(
                $names = array_keys($data['parameters'] ?? []),
                array_map(
                    static fn (string $name) => Parameter::build($data['parameters'][$name]),
                    $names,
                )
            ),
            array_combine(
                $names = array_keys($data['requestBodies'] ?? []),
                array_map(
                    static fn (string $name) => RequestBody::build($data['requestBodies'][$name]),
                    $names,
                )
            ),
            array_combine(
                $names = array_keys($data['headers'] ?? []),
                array_map(
                    static fn (string $name) => Header::build($data['headers'][$name]),
                    $names,
                )
            ),
        );
    }

    private function __construct(
        /** @var array<Schema> */
        public readonly array $schemas,
        /** @var array<Response> */
        public readonly array $responses,
        /** @var array<Parameter> */
        public readonly array $parameters,
        /** @var array<RequestBody> */
        public readonly array $requestBodies,
        /** @var array<Header> */
        public readonly array $headers,
    ) {
    }
}