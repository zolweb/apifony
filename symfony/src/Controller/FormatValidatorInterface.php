<?php

namespace App\Controller;

interface FormatValidatorInterface
{
    /**
     * @return array<string>
     */
    public function validate($value): array;
}