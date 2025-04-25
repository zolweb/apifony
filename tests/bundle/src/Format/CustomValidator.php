<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
class CustomValidator extends ConstraintValidator
{
    private CustomDefinition $formatDefinition;
    public function setFormatDefinition(CustomDefinition $formatDefinition): void
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