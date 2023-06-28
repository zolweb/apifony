<?php

namespace tests\Validator;

class F12Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F12Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}