<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F4Validator extends ConstraintValidator
{
    public function __construct(
        private readonly F4Definition $formatDefinition,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        foreach ($this->formatDefinition->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}