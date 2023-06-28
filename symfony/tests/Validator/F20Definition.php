<?php

namespace tests\Validator;

class F20Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F20Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}