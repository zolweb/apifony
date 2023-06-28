<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

use App\Zol\Invoicing\Presentation\Api\Bundle\Model\Test;

class PostTest201ApplicationJsonResponse
{
    public const CODE = '201';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly Test $payload,
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
