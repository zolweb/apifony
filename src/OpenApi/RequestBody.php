<?php

declare(strict_types=1);

namespace Zol\Ogen\OpenApi;

class RequestBody
{
    /**
     * @param array<mixed> $data
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function build(array $data, array $path): self
    {
        if (isset($data['required']) && !\is_bool($data['required'])) {
            throw new Exception('RequestBody object required attribute must be a boolean.', $path);
        }

        $content = [];
        if (isset($data['content'])) {
            if (!\is_array($data['content'])) {
                throw new Exception('RequestBody object content attribute must be an array.', $path);
            }
            foreach ($data['content'] as $type => $contentData) {
                if (!\is_string($type)) {
                    throw new Exception('RequestBody object content attribute keys must be strings.', $path);
                }
                if (!\is_array($contentData)) {
                    throw new Exception('RequestBody object content attribute elements must be objects.', $path);
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

        return new self($data['required'] ?? false, $content, $extensions, $path);
    }

    /**
     * @param array<string, MediaType> $content
     * @param array<string, mixed>     $extensions
     * @param list<string>             $path
     */
    private function __construct(
        public readonly bool $required,
        public readonly array $content,
        public readonly array $extensions,
        public readonly array $path,
    ) {
    }
}
