<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\User;

class CreateUsersWithListInput200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly User $payload,
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
