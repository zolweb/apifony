<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;

use \Model\User;

class CreateUser201ApplicationJsonResponse
{
    public const CODE = '201';
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
