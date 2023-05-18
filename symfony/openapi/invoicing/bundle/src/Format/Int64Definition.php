<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface Int64Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}