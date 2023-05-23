<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F9Validator extends ConstraintValidator
{
    public function __construct(
        private readonly F9Definition $formatDefinition,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        foreach ($this->formatDefinition->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}