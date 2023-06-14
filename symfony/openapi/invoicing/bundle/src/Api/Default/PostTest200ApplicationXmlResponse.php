<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

class PostTest200ApplicationXmlResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/xml';

    public function __construct(
        public readonly PostTest200ApplicationXmlResponse $payload,
        public readonly string $H1,
        public readonly int $H2,
        public readonly float $H3,
        public readonly bool $H4,
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return [
            'h1' => strval($this->H1),
            'h2' => strval($this->H2),
            'h3' => strval($this->H3),
            'h4' => strval($this->H4),
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
