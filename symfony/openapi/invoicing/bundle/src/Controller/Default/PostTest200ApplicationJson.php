<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Controller\Default;

class PostTest200ApplicationJson{
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
     * @array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'h1' => $this->h1,
            'h2' => $this->h2,
            'h3' => $this->h3,
            'h4' => $this->h4,
            'content-type' => self::CONTENT_TYPE,
        ];
    }
}