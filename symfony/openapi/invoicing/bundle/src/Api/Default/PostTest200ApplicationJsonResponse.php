<?php

namespace AppZolInvoicingPresentationApiBundle\Api\Default;

use AppZolInvoicingPresentationApiBundle\Model\Test;

class PostTest200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';


    public function __construct(
        public readonly Test $payload,
        public readonly string $h1,
        public readonly int $h2,
        public readonly float $h3,
        public readonly bool $h4,
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'h1' => strval($this->h1),
            'h2' => strval($this->h2),
            'h3' => strval($this->h3),
            'h4' => strval($this->h4),
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
