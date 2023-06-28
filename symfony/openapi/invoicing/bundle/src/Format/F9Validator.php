<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F9Validator extends ConstraintValidator
{
    private F9Definition $formatDefinition;

    public function setFormatDefinition(F9Definition $formatDefinition): void
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