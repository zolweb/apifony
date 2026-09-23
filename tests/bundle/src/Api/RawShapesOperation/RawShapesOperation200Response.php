<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\RawShapesOperation;

class RawShapesOperation200Response
{
    public function __construct(public readonly RawShapesOperation200ResponsePayload $payload)
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
        return ['content-type' => 'application/json'];
    }
}