<?php

namespace App\Controller;

interface UuidValidatorInterface
{
    /**
     * @return array<string>
     */
    public function validate($value): array;
}