<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F6Validator extends ConstraintValidator
{
    public function __construct(
        private readonly F6Definition $formatDefinition,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        foreach ($this->formatDefinition->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}