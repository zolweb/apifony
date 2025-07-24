<?php

declare(strict_types=1);

namespace Zol\Apifony\Tests;

class CustomDefinition implements TestOpenApiServer\Format\CustomDefinition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}
