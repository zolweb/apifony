<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\User;


class UpdateUser201EmptyResponse
{
    public const CODE = '201';
    public const CONTENT_TYPE = null;

    public readonly string $payload;

    public function __construct(
    ) {
        $this->payload = '';
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
