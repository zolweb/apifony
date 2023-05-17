<?php

namespace App\Controller;

interface F1Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}