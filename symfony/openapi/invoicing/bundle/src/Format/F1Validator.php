<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F1Validator extends ConstraintValidator
{
    private F1Definition $formatDefinition;

    public function setFormatDefinition(F1Definition $formatDefinition): void
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