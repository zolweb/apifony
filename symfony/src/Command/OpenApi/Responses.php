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
        foreach ($data as $code => $responseData) {
            if (!is_string($code)) {
                throw new Exception('Responses object array keys must be strings.');
            }
            if (!is_array($responseData)) {
                throw new Exception('Responses object array elements must be objects.');
            }
            $responses[$code] = isset($responseData['$ref']) ? Reference::build($responseData) : Response::build($responseData);
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