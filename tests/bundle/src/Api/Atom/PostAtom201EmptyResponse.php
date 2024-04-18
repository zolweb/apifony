<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

class PostAtom201EmptyResponse
{
    public const CODE = '201';
    public const CONTENT_TYPE = null;
    public readonly string $payload;
    public function __construct()
    {
        $this->payload = '';
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['content-type' => self::CONTENT_TYPE];
    }
}