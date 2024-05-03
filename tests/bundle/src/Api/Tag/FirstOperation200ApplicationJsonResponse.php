<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api\Tag;

class FirstOperation200ApplicationJsonResponse
{
    public const CODE = '200';
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly FirstOperation200ApplicationJsonResponsePayload $payload, public readonly string $headerString, public readonly float $headerNumber, public readonly int $headerInteger, public readonly bool $headerBoolean)
    {
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['headerString' => strval($this->headerString), 'headerNumber' => strval($this->headerNumber), 'headerInteger' => strval($this->headerInteger), 'headerBoolean' => strval($this->headerBoolean), 'content-type' => self::CONTENT_TYPE];
    }
}