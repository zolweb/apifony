<?php

namespace App\Controller;

interface F3Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}