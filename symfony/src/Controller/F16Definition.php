<?php

namespace App\Controller;

interface F16Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}