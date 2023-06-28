<?php

namespace AppZolInvoicingPresentationApiBundle\Api\User;

use AppZolInvoicingPresentationApiBundle\Model\User;

class GetUserByName200ApplicationJsonResponse
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
