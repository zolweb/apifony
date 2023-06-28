<?php

namespace tests\Validator;

class F1Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F1Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}