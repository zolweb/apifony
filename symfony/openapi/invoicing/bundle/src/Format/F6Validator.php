<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F6Validator extends ConstraintValidator
{
    private F6Definition $formatDefinition;

    public function setFormatDefinition(F6Definition $formatDefinition): void
    {
        $this->formatDefinition = $formatDefinition;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        foreach ($this->formatDefinition->validate($value) as $violation) {
            $this->context->buildViolation($violation)->addViolation();
        }
    }
}