<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;

use Zol\Ogen\Tests\TestOpenApiServer\Model\Content;
class GetContent200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly Content $payload)
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