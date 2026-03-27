<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

use Egulias\EmailValidator\EmailValidator as EguliasEmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
class EmailValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (\is_string($value)) {
            if (\strlen($value)) {
                $emailValidator = new EguliasEmailValidator();
                if (!$emailValidator->isValid($value, new RFCValidation())) {
                    $this->context->buildViolation('This value is not a valid email address.')->addViolation();
                }
            } else {
                $this->context->buildViolation('This value should not be blank.')->addViolation();
            }
        }
    }
}