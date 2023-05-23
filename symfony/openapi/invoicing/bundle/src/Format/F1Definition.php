<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface F1Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}