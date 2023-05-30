<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

class PostTest201EmptyResponse
{
    public const CODE = '201';
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
