<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

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
    public function deserialize(string $json, string $type) : object;
    /**
     * @template T of object
     *
     * @param array<mixed> $data
     * @param class-string<T> $type
     *
     * @return T
     *
     * @throws ExceptionInterface
     * @throws \TypeError
     */
    public function denormalize(array $data, string $type) : object;
}