<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\Tag;

class FirstOperation200Response
{
    public const CODE = 200;
    public const CONTENT_TYPE = 'application/json';
    public function __construct(public readonly FirstOperation200ResponsePayload $payload, public readonly string $headerString, public readonly float $headerNumber, public readonly int $headerInteger, public readonly bool $headerBoolean)
    {
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['headerString' => (string) $this->headerString, 'headerNumber' => (string) $this->headerNumber, 'headerInteger' => (string) $this->headerInteger, 'headerBoolean' => (string) $this->headerBoolean, 'content-type' => self::CONTENT_TYPE];
    }
}