<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

class UpdatePet200ApplicationXmlResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/xml';

    public function __construct(
        public readonly UpdatePet200ApplicationXmlResponse $payload,
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
