<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

class LoginUser200ApplicationJson
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly string $payload,
        public readonly int $XRateLimit,
        public readonly string $XExpiresAfter,
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'X-Rate-Limit' => $this->XRateLimit,
            'X-Expires-After' => $this->XExpiresAfter,
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
