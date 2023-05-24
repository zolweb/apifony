<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api\Default;

class PostTest200ApplicationJson
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        public readonly PostTest200ApplicationJson $payload,
        public readonly string $H1,
        public readonly int $H2,
        public readonly float $H3,
        public readonly bool $H4,
    ) {
    }

    /**
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'h1' => $this->H1,
            'h2' => $this->H2,
            'h3' => $this->H3,
            'h4' => $this->H4,
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}
