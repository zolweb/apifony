<?php

namespace App\Controller;

class LoginUser200ApplicationJson{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly string $payload,
        public readonly int $xRateLimit,
        public readonly string $xExpiresAfter,
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'X-Rate-Limit' => $this->xRateLimit,
            'X-Expires-After' => $this->xExpiresAfter,
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}