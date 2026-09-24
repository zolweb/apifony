<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
class IgnoredFormatValidator extends ConstraintValidator
{
    private IgnoredFormatDefinition $formatDefinition;
    public function setFormatDefinition(IgnoredFormatDefinition $formatDefinition): void
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