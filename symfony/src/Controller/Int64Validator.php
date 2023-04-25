<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Int64Validator extends ConstraintValidator
{
    public function __construct(
        private readonly Int64ValidatorInterface $validator,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        foreach ($this->validator->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}