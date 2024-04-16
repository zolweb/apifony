<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;


class PostContentTranslation201EmptyResponse
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
