<?php

namespace App\Controller;

class TestRespApplicationJson{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly TestRespApplicationJsonSchema $payload,
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