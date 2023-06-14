<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

class PlaceOrder200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly PlaceOrder200ApplicationJsonResponse $payload,
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
