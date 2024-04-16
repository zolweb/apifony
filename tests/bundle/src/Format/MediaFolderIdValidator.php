<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MediaFolderIdValidator extends ConstraintValidator
{
    private MediaFolderIdDefinition $formatDefinition;

    public function setFormatDefinition(MediaFolderIdDefinition $formatDefinition): void
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