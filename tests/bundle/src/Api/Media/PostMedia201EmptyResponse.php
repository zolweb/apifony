<?php

namespace Zol\TestOpenApiServer\Api\Media;


class PostMedia201EmptyResponse
{
    public const CODE = '201';
    public const CONTENT_TYPE = null;

    public readonly string $payload;

    public function __construct(
        public readonly string $location,
    ) {
        $this->payload = '';
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'Location' => strval($this->location),
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
