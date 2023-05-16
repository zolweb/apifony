<?php

namespace App\Controller;

interface F15Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}