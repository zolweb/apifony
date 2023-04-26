<?php

namespace App\Controller;

interface Int64ValidatorInterface
{
    /**
     * @return array<string>
     */
    public function validate($value): array;
}