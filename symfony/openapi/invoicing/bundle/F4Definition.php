<?php

namespace App\Controller;

interface F4Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}