<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

interface CustomDefinition
{
    /**
     * @return string[]
     */
    public function validate(mixed $value): array;
}