<?php

namespace tests\Validator;

class F7Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F7Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}