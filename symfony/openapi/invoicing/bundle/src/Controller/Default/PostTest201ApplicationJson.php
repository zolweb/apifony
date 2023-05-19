<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Default;

class PostTest201ApplicationJson{
    public const CODE = '201';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly Test $payload,
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