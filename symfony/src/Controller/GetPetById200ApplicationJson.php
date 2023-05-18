<?php

namespace App\Controller;

class GetPetById200ApplicationJson{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly Pet $payload,
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