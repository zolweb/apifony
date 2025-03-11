<?php

declare(strict_types=1);

namespace Zol\Ogen\Tests\TestOpenApiServer\Format;

interface CustomDefinition
{
    /**
     * @return string[]
     */
    public function validate(mixed $value): array;
}
