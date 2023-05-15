<?php

namespace App\Command;

class Response
{
    public readonly Operation $operation;
    public readonly string $code;
    public readonly array $headers;
    public readonly array $mediaTypes;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(Operation $operation, string $code, array $componentsData, array $data): self
    {
        $response = new self();
        $response->operation = $operation;
        $response->code = $code;
        $response->headers = array_map(
            fn (string $name) => Parameter::build($response, $componentsData, $data['headers'][$name]),
            array_keys($data['headers'] ?? []),
        );
        $response->mediaTypes = array_map(
            fn (string $type) => MediaType::build($response, $type, $componentsData, $data['content'][$type]),
            array_keys(
                array_filter(
                    $data['content'] ?? [],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );

        return $response;
    }

    private function __construct()
    {
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (MediaType $mediaType) => $mediaType->getFiles(),
                $this->mediaTypes,
            ),
        );
    }
}