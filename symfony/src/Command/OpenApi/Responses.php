<?php

namespace App\Command\OpenApi;

class Responses
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $responses = [];
        foreach ($data as $code => $response) {
            if (!is_string($code)) {
                throw new Exception('Responses object array keys must be strings.');
            }
            if (!is_array($response)) {
                throw new Exception('Responses object array elements must be objects.');
            }
            $responses[$code] = isset($response['$ref']) ? Reference::build($response) : Response::build($response);
        }

        return new self($responses);
    }

    /**
     * @param array<string, Reference|Response> $responses
     */
    private function __construct(
        public readonly array $responses,
    ) {
    }
}