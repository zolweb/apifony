<?php

namespace App\Controller;

interface DateTimeDefinition
{
    /**
     * @return array<string>
     */
    public function validate(string $value): array;
}