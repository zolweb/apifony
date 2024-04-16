<?php

namespace Zol\TestOpenApiServer\Api\ContentType;

use Zol\TestOpenApiServer\Model\ContentType;

class GetContentType200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly ContentType $payload,
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
