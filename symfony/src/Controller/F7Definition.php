<?php

namespace App\Controller;

interface F7Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}