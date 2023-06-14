<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Store;

class DeleteOrder200EmptyResponse
{
    public const CODE = '200';
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
