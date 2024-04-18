<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

class PostAtom400ApplicationJsonResponse
{
    public const CODE = '400';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly PostAtom400ApplicationJsonResponsePayload $payload)
    {
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['content-type' => self::CONTENT_TYPE];
    }
}