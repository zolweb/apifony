<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UuidValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UuidValidatorInterface $validator,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        foreach ($this->validator->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}