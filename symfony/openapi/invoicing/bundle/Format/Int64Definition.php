<?php

namespace App\Controller;

interface Int64Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}