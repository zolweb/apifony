<?php

namespace tests\Validator;

class F10Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F10Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}