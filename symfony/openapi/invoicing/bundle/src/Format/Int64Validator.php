<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Int64Validator extends ConstraintValidator
{
    public function __construct(
        private readonly Int64Definition $formatDefinition,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        foreach ($this->formatDefinition->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}