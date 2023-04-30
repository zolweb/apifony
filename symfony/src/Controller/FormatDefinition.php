<?php

namespace App\Controller;

interface FormatDefinition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}