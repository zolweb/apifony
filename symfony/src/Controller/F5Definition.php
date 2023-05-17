<?php

namespace App\Controller;

interface F5Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}