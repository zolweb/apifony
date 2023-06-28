<?php

namespace tests\Validator;

class F6Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F6Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}