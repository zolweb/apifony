<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\MediaTree;

class GetMediaFolder404ApplicationJsonResponse
{
    public const CODE = '404';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly GetMediaFolder404ApplicationJsonResponsePayload $payload)
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