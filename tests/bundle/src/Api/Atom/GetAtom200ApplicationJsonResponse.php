<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Atom;

class GetAtom200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly GetAtom200ApplicationJsonResponsePayload $payload)
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