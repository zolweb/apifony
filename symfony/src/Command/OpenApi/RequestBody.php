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
        if (isset($data['contents'])) {
            if (!is_array($data['contents'])) {
                throw new Exception('RequestBody object content attribute must be an array.');
            }
            foreach ($data['contents'] as $type => $contentData) {
                if (!is_string($type)) {
                    throw new Exception('RequestBody object content attribute keys must be strings.');
                }
                if (!is_array($contentData)) {
                    throw new Exception('RequestBody object content attribute elements must be objects.');
                }
                $content[$type] = MediaType::build($contentData);
            }
        }

        return new self($data['required'] ?? false, $content);
    }

    /**
     * @param array<string, MediaType> $content
     */
    private function __construct(
        public readonly bool $required,
        public readonly array $content,
    ) {
    }
}