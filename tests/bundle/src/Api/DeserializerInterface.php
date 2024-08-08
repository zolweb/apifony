<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

interface DeserializerInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function deserialize(string $json, string $type): object;
}