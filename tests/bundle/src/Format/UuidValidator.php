<?php

namespace Zol\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UuidValidator extends ConstraintValidator
{
    private UuidDefinition $formatDefinition;

    public function setFormatDefinition(UuidDefinition $formatDefinition): void
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