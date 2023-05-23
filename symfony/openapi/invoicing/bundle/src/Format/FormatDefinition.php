<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface FormatDefinition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}