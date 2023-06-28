<?php

namespace tests\Validator;

class F18Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F18Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}