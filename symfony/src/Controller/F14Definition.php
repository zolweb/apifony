<?php

namespace App\Controller;

interface F14Definition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}