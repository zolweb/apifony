<?php

namespace App\Handler;

class DateTimeDefinition implements \App\Controller\DateTimeDefinition
{
    public function validate(string $value): array
    {
        return [];
    }
}
