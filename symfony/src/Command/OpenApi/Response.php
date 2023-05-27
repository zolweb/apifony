<?php

namespace App\Command\OpenApi;

class Response
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        $headers = [];
        if (isset($data['headers'])) {
            if (!is_array($data['headers'])) {
                throw new Exception('Response object headers attribute must be an array.');
            }
            foreach ($data['headers'] as $name => $header) {
                if (!is_string($name)) {
                    throw new Exception('Response object headers attribute keys must be strings.');
                }
                if (!is_array($header)) {
                    throw new Exception('Response object headers attribute elements must be objects.');
                }
                $headers[$name] = isset($header['$ref']) ? Reference::build($header) : Header::build($header);
            }
        }

        $contents = [];
        if (isset($data['contents'])) {
            if (!is_array($data['contents'])) {
                throw new Exception('Response object contents attribute must be an array.');
            }
            foreach ($data['contents'] as $type => $content) {
                if (!is_string($type)) {
                    throw new Exception('Response object contents attribute keys must be strings.');
                }
                if (!is_array($content)) {
                    throw new Exception('Response object contents attribute elements must be objects.');
                }
                $contents[$type] = MediaType::build($content);
            }
        }

        return new self($headers, $contents);
    }

    /**
     * @param array<string, Reference|Header> $headers
     * @param array<string, MediaType> $content
     */
    private function __construct(
        public readonly array $headers,
        public readonly array $content,
    ) {
    }
}