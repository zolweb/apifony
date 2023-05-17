<?php

namespace App\Controller;

interface F17Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}