<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FormatValidator extends ConstraintValidator
{
    private FormatDefinition $formatDefinition;

    public function setFormatDefinition(FormatDefinition $formatDefinition): void
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