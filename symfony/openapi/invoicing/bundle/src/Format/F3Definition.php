<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface F3Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}