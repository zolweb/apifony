<?php

namespace App\Controller;

interface F10Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}