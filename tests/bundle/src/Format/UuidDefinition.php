<?php

namespace Zol\TestOpenApiServer\Format;

interface UuidDefinition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}