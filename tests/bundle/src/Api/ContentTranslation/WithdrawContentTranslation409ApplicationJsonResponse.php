<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\ContentTranslation;


class WithdrawContentTranslation409ApplicationJsonResponse
{
    public const CODE = '409';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly WithdrawContentTranslation409ApplicationJsonResponsePayload $payload,
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
