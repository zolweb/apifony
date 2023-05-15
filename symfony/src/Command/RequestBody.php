<?php

namespace App\Command;

use function Symfony\Component\String\u;

class RequestBody
{
    public readonly Operation $operation;
    public readonly bool $required;
    /** @var array<MediaType> */
    public readonly array $mediaTypes;

    /**
     * @throws Exception
     */
    public static function build(
        Operation $operation,
        string $className,
        array $componentsData,
        array $data,
    ): self {
        if (isset($data['$ref'])) {
            $data = $componentsData['requestBodies'][explode('/', $data['$ref'])[3]];
        }

        $requestBody = new self();
        $requestBody->operation = $operation;
        $requestBody->required = $data['required'] ?? false;
        $requestBody->mediaTypes = array_map(
            fn (string $type) => MediaType::build(
                $requestBody,
                sprintf('%s%sMediaType', $className, u($type)->camel()->title()),
                $type,
                $componentsData,
                $data['content'][$type],
            ),
            array_keys(
                array_filter(
                    $data['content'],
                    static fn (string $type) => in_array($type, ['application/json'], true),
                    ARRAY_FILTER_USE_KEY,
                ),
            ),
        );

        return $requestBody;
    }

    private function __construct()
    {
    }

    public function getMethodParameterType(): string
    {
        return 'Lol';
    }

    public function getFiles(): array
    {
        return array_merge(
            ...array_map(
                static fn (MediaType $mediaType) => $mediaType->getFiles(),
                $this->mediaTypes,
            ),
        );
    }
}