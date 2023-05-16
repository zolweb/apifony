<?php

namespace App\Controller;

interface F19Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}