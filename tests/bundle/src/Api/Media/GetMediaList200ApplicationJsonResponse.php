<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

class GetMediaList200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly GetMediaList200ApplicationJsonResponsePayload $payload)
    {
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['content-type' => self::CONTENT_TYPE];
    }
}