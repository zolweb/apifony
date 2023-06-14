<?php

namespace App\Command\OpenApi;

class RequestBody
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(array $data): self
    {
        if (isset($data['required']) && !is_bool($data['required'])) {
            throw new Exception('RequestBody object required attribute must be a boolean.');
        }

        $content = [];
        if (isset($data['content'])) {
            if (!is_array($data['content'])) {
                throw new Exception('RequestBody object content attribute must be an array.');
            }
            foreach ($data['content'] as $type => $contentData) {
                if (!is_string($type)) {
                    throw new Exception('RequestBody object content attribute keys must be strings.');
                }
                if (!is_array($contentData)) {
                    throw new Exception('RequestBody object content attribute elements must be objects.');
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

        return new self($data['required'] ?? false, $content, $extensions);
    }

    /**
     * @param array<string, MediaType> $content
     * @param array<string, mixed> $extensions
     */
    private function __construct(
        public readonly bool $required,
        public readonly array $content,
        public readonly array $extensions,
    ) {
    }
}