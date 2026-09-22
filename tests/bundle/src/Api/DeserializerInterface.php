<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
interface DeserializerInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     *
     * @throws ExceptionInterface
     * @throws \TypeError
     */
    public function deserialize(string $json, string $type): object;
}