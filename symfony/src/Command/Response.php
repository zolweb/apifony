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
                sprintf('%s%sMediaType', $className, u($type)->camel()->title()),
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

    /**
     * @return array<string, Schema>
     */
    public function getAllPossibleResponseBodyPayloadTypes(): array
    {
        $responseBodyContentTypes = [];

        foreach ($this->content as $mediaType) {
            $responseBodyContentTypes[$mediaType->schema->type->getNormalizedType()] = $mediaType->schema;
        }

        return $responseBodyContentTypes;
    }

    public function addFiles(array& $files): void
    {
        foreach ($this->headers as $header) {
            $header->addFiles($files);
        }
        foreach ($this->content as $mediaType) {
            $mediaType->addFiles($files);
        }
        if (count($this->getAllPossibleResponseBodyPayloadTypes()) > 0) {
            foreach ($this->getAllPossibleResponseBodyPayloadTypes() as $payloadType) {
                if (!isset($files["{$this->className}{$this->code}{$payloadType->type->getNormalizedType()}"])) {
                    $files["{$this->className}{$this->code}{$payloadType->type->getNormalizedType()}"] =
                        ['template' => 'response.php.twig', 'params' => [
                            'className' => "{$this->className}{$this->code}{$payloadType->type->getNormalizedType()}",
                            'response' => $this,
                            'payloadType' => $payloadType,
                        ]];
                }
            }
        } else {
            $files["{$this->className}{$this->code}Empty"] =
                ['template' => 'response.php.twig', 'params' => [
                    'className' => "{$this->className}{$this->code}Empty",
                    'response' => $this,
                    'payloadType' => null,
                ]];
        }
    }
}