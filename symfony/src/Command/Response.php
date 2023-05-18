<?php

namespace App\Command;

use function Symfony\Component\String\u;

class Response
{
    public readonly string $className;
    public readonly string $code;
    /** @var array<Header> */
    public readonly array $headers;
    /** @var array<MediaType> */
    public readonly array $content;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, string $code, array& $components, array $data): self
    {
        $response = new self();

        if (isset($data['$ref'])) {
            $className = explode('/', $data['$ref'])[3];
            $component = &$components['responses'][$className];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $response;
                $data = $component['data'];
            }
        }

        $response->className = $className;
        $response->code = $code;
        $response->headers = array_map(
            fn (string $name) => Header::build(
                sprintf('%s%sHeader', $className, u($name)->camel()->title()),
                $name,
                $components,
                $data['headers'][$name],
            ),
            array_keys($data['headers'] ?? []),
        );
        $response->content = array_map(
            fn (string $type) => MediaType::build(
                sprintf('%s%s', $className, u($type)->camel()->title()),
                $type,
                $components,
                $data['content'][$type],
            ),
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

    public function addFiles(array& $files, string $folder): void
    {
        foreach ($this->headers as $header) {
            $header->addFiles($files, $folder);
        }
        foreach ($this->content as $mediaType) {
            if (!isset($files["{$folder}/{$mediaType->className}.php"])) {
                $files["{$folder}/{$mediaType->className}.php"] = [
                    'folder' => $folder,
                    'name' => "{$mediaType->className}.php",
                    'template' => 'response.php.twig',
                    'params' => [
                        'response' => $this,
                        'mediaType' => $mediaType,
                    ],
                ];
                $mediaType->addFiles($files, $folder);
            }
        }
        if (count($this->content) === 0 && !isset($files["{$folder}/{$this->className}Empty.php"])) {
            $files["{$folder}/{$this->className}Empty.php"] = [
                'folder' => $folder,
                'name' => "{$this->className}Empty.php",
                'template' => 'response.php.twig',
                'params' => [
                    'response' => $this,
                    'mediaType' => null,
                ],
            ];
        }
    }
}