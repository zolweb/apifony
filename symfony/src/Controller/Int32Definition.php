<?php

namespace App\Controller;

interface Int32Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}