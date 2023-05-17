<?php

namespace App\Controller;

interface F13Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}