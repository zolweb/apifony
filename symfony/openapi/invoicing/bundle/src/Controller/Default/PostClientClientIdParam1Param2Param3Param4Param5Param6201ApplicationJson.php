<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Default;

class PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJson{
    public const CODE = '201';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly PostClientClientIdParam1Param2Param3Param4Param5Param6201ApplicationJsonSchema $payload,
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}