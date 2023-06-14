<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse
{
    public const CODE = '201';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonResponse $payload,
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
