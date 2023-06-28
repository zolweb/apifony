<?php

namespace AppZolInvoicingPresentationApiBundle\Api\User;


class LoginUser200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly string $payload,
        public readonly int $xRateLimit,
        public readonly string $xExpiresAfter,
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'X-Rate-Limit' => strval($this->xRateLimit),
            'X-Expires-After' => strval($this->xExpiresAfter),
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
