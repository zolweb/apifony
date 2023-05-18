<?php

namespace App\Controller;

interface F21Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}