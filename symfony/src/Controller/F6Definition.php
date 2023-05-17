<?php

namespace App\Controller;

interface F6Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}