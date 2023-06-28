<?php

namespace tests\Validator;

class F9Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F9Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}