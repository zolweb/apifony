<?php

declare(strict_types=1);

namespace Zol\Apifony\OpenApi;

class Response
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        $headers = [];
        if (isset($data['headers'])) {
            if (!\is_array($data['headers'])) {
                throw new Exception('Response object headers attribute must be an array.', $path);
            }
            foreach ($data['headers'] as $name => $headerData) {
                if (!\is_string($name)) {
                    throw new Exception('Response object headers attribute keys must be strings.', $path);
                }
                if (!\is_array($headerData)) {
                    throw new Exception('Response object headers attribute elements must be objects.', $path);
                }
                $headerPath = $path;
                $headerPath[] = 'headers';
                $headerPath[] = $name;
                $headers[$name] = isset($headerData['$ref']) ? Reference::build($headerData, $headerPath) : Header::build($headerData, $headerPath);
            }
        }

        $content = [];
        if (isset($data['content'])) {
            if (!\is_array($data['content'])) {
                throw new Exception('Response object content attribute must be an array.', $path);
            }
            foreach ($data['content'] as $type => $contentData) {
                if (!\is_string($type)) {
                    throw new Exception('Response object content attribute keys must be strings.', $path);
                }
                if (!\is_array($contentData)) {
                    throw new Exception('Response object content attribute elements must be objects.', $path);
                }
                $mediaTypePath = $path;
                $mediaTypePath[] = 'content';
                $mediaTypePath[] = $type;
                $content[$type] = MediaType::build($contentData, $mediaTypePath);
            }
        }

        $extensions = [];
        foreach ($data as $key => $extension) {
            if (\is_string($key) && str_starts_with($key, 'x-')) {
                $extensions[$key] = $extension;
            }
        }

        return new self($headers, $content, $extensions, $path);
    }

    /**
     * @param array<string, Reference|Header> $headers
     * @param array<string, MediaType>        $content
     * @param array<string, mixed>            $extensions
     * @param list<string>                    $path
     */
    private function __construct(
        public readonly array $headers,
        public readonly array $content,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
