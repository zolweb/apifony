<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api\ComponentBucketOperation;

use Zol\Apifony\Tests\TestOpenApiServer\Model\Abc;
class ComponentBucketOperation200Response
{
    public function __construct(public readonly Abc $payload, public readonly string $bucketRefHeader, public readonly string $bucketRefSchemaHeader)
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
        return ['bucketRefHeader' => (string) $this->bucketRefHeader, 'bucketRefSchemaHeader' => (string) $this->bucketRefSchemaHeader, 'content-type' => 'application/json'];
    }
}