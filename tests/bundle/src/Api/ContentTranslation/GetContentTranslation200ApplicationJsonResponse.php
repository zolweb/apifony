<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;

class GetContentTranslation200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly ContentTranslation $payload)
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
use Zol\Ogen\Tests\TestOpenApiServer\Model\{ContentTranslation};