<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Content;


class DuplicateContent404ApplicationJsonResponse
{
    public const CODE = '404';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly DuplicateContent404ApplicationJsonResponsePayload $payload,
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
