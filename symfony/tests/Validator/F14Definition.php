<?php

namespace tests\Validator;

class F14Definition implements \App\Zol\Invoicing\Presentation\Api\Bundle\Format\F14Definition
{
    public function validate(mixed $value): array
    {
        return [];
    }
}