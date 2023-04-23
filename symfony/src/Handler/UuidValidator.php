<?php

namespace App\Handler;

use App\Controller\UuidValidatorInterface;

class UuidValidator implements UuidValidatorInterface
{
    public function validate($value): array
    {
        return [];
    }
}