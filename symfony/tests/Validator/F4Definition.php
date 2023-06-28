<?php

namespace tests\Validator;

class F4Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F4Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}