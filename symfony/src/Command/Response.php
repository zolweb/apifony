<?php

namespace App\Command;

class Response
{
    public readonly Responses $responses;
    public readonly string $code;
    public readonly array $headers;
    public readonly array $mediaTypes;

    /**
     * @param array<mixed> $componentsData
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(Responses $responses, string $code, array $componentsData, array $data): self
    {
        if (isset($data['$ref'])) {
            $data = $componentsData['responses'][explode('/', $data['$ref'])[3]];
        }

        $response = new self();
        $response->responses = $responses;
        $response->code = $code;
        $response->headers = array_map(
            fn (string $name) => Parameter::build($response, $componentsData, $data['headers'][$name]),
            array_keys($data['headers'] ?? []),
        );
        $response->mediaTypes = array_map(
            fn (string $type) => MediaType::build($response, $type, $componentsData, $data['content'][$type]),
            array_filter(
                array_keys($data['content'] ?? []),
                static fn (string $type) => in_array($type, ['application/json'], true),
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