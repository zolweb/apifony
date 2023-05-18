<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

interface Int32Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}