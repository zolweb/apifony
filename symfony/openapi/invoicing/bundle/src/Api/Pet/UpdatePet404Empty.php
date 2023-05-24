<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

class UpdatePet404Empty
{
    public const CODE = '404';
    public const CONTENT_TYPE = null;

    public function __construct(
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
