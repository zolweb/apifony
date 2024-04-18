<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\MediaTree;

class DeleteMediaFolder409ApplicationJsonResponse
{
    public const CODE = '409';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly DeleteMediaFolder409ApplicationJsonResponsePayload $payload)
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