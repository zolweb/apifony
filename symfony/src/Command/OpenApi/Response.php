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
            foreach ($data['headers'] as $name => $headerData) {
                if (!is_string($name)) {
                    throw new Exception('Response object headers attribute keys must be strings.');
                }
                if (!is_array($headerData)) {
                    throw new Exception('Response object headers attribute elements must be objects.');
                }
                $headers[$name] = isset($headerData['$ref']) ? Reference::build($headerData) : Header::build($headerData);
            }
        }

        $content = [];
        if (isset($data['contents'])) {
            if (!is_array($data['contents'])) {
                throw new Exception('Response object content attribute must be an array.');
            }
            foreach ($data['contents'] as $type => $contentData) {
                if (!is_string($type)) {
                    throw new Exception('Response object content attribute keys must be strings.');
                }
                if (!is_array($contentData)) {
                    throw new Exception('Response object content attribute elements must be objects.');
                }
                $content[$type] = MediaType::build($contentData);
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self($headers, $content, $extensions);
    }

    /**
     * @param array<string, Reference|Header> $headers
     * @param array<string, MediaType> $content
     * @param array<string, mixed> $extensions
     */
    private function __construct(
        public readonly array $headers,
        public readonly array $content,
        public readonly array $extensions,
    ) {
    }
}