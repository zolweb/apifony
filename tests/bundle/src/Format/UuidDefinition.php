<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Format;

interface UuidDefinition
{
    /**
     * @return string[]
     */
    public function validate(mixed $value): array;
}