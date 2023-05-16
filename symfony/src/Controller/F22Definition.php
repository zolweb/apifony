<?php

namespace App\Controller;

interface F22Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}