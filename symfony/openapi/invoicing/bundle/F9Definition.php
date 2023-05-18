<?php

namespace App\Controller;

interface F9Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}