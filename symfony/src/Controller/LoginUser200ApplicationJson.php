<?php

namespace App\Controller;

class LoginUser200ApplicationJson{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly string $payload,
        public readonly int $X-Rate-Limit,
        public readonly string $X-Expires-After,
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'X-Rate-Limit' => $this->X-Rate-Limit,
            'X-Expires-After' => $this->X-Expires-After,
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}