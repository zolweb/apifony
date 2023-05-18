<?php

namespace App\Controller;

interface F25Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}