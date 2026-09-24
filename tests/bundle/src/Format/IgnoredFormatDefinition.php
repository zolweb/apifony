<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

interface IgnoredFormatDefinition
{
    /**
     * @return string[]
     */
    public function validate(mixed $value): array;
}