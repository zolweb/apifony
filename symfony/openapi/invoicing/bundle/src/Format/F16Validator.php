<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class F16Validator extends ConstraintValidator
{
    private F16Definition $formatDefinition;

    public function setFormatDefinition(F16Definition $formatDefinition): void
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