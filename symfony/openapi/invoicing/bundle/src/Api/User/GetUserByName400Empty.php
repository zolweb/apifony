<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

class GetUserByName400Empty
{
    public const CODE = '400';
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
