<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\FirstOperation;

class FirstOperation200Response
{
    public function __construct(public readonly FirstOperation200ResponsePayload $payload, public readonly string $headerString, public readonly float $headerNumber, public readonly int $headerInteger, public readonly bool $headerBoolean)
    {
    }
    public function getCode(): int
    {
        return 200;
    }
    public function getContentType(): ?string
    {
        return 'application/json';
    }
    /**
     * @return array<string, ?string>
     */
    public function getHeaders(): array
    {
        return ['headerString' => (string) $this->headerString, 'headerNumber' => (string) $this->headerNumber, 'headerInteger' => (string) $this->headerInteger, 'headerBoolean' => (string) $this->headerBoolean, 'content-type' => 'application/json'];
    }
}