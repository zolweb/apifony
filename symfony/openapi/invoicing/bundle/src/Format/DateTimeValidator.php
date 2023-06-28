<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeValidator extends ConstraintValidator
{
    private DateTimeDefinition $formatDefinition;

    public function setFormatDefinition(DateTimeDefinition $formatDefinition): void
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