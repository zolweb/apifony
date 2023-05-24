<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Pet;

class AddPet405Empty
{
    public const CODE = '405';
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
