<?php

namespace App\Controller;

interface F18Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}