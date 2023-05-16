<?php

namespace App\Command;

use function Symfony\Component\String\u;

class RequestBody
{
    public readonly string $className;
    public readonly bool $required;
    /** @var array<MediaType> */
    public readonly array $mediaTypes;

    /**
     * @param array<mixed> $components
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function build(string $className, array& $components, array $data): self
    {
        $requestBody = new self();

        if (isset($data['$ref'])) {
            $className = explode('/', $data['$ref'])[3];
            $component = &$components['requestBodies'][$className];
            if ($component['instance'] !== null) {
                return $component['instance'];
            } else {
                $component['instance'] = $requestBody;
                $data = $component['data'];
            }
        }

        $requestBody->className = $className;
        $requestBody->required = $data['required'] ?? false;
        $requestBody->mediaTypes = array_map(
            fn (string $type) => MediaType::build(
                sprintf('%s%sMediaType', $className, u($type)->camel()->title()),
                $type,
                $components,
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

    public function addFiles(array& $files): void
    {
        foreach ($this->mediaTypes as $mediaType) {
            $mediaType->addFiles($files);
        }
    }
}