<?php

namespace tests\Validator;

class F23Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F23Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}