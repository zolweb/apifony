<?php

namespace App\Controller;

interface F12Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}