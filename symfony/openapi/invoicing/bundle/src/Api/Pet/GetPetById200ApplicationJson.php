<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

class GetPetById200ApplicationJson
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly GetPetById200ApplicationJson $payload,
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
