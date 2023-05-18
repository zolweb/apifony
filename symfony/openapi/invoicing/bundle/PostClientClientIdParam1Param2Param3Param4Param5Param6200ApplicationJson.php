<?php

namespace App\Controller;

class PostClientClientIdParam1Param2Param3Param4Param5Param6200ApplicationJson{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly string $payload,
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