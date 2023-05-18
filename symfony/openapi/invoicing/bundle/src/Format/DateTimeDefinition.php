<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface DateTimeDefinition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}