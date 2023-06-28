<?php

namespace AppZolInvoicingPresentationApiBundle\Api\User;


class CreateUsersWithListInput100EmptyResponse
{
    public const CODE = '100';
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
