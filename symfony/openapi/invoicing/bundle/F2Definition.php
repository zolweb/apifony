<?php

namespace App\Controller;

interface F2Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}