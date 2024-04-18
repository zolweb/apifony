<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

class PostContent409ApplicationJsonResponse
{
    public const CODE = '409';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly PostContent409ApplicationJsonResponsePayload $payload)
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