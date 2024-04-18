<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

class ArchiveMedia400ApplicationJsonResponse
{
    public const CODE = '400';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly ArchiveMedia400ApplicationJsonResponsePayload $payload)
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