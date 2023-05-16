<?php

namespace App\Controller;

interface F23Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}