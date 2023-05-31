<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

class UpdatePetWithForm405EmptyResponse
{
    public const CODE = '405';
    public const CONTENT_TYPE = null;

    public function __construct(
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
