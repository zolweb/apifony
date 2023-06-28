<?php

namespace tests\Validator;

class F21Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F21Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}