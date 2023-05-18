<?php

namespace App\Controller;

interface F20Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}