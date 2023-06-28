<?php

namespace tests\Validator;

class F5Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F5Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}