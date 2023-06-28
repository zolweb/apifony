<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F7Validator extends ConstraintValidator
{
    private F7Definition $formatDefinition;

    public function setFormatDefinition(F7Definition $formatDefinition): void
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