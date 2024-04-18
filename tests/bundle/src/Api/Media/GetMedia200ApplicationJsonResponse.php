<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Media;

class GetMedia200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly Media $payload)
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
use Zol\Ogen\Tests\TestOpenApiServer\Model\{Media};