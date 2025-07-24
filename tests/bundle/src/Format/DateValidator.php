<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validation;
class DateValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (\is_string($value)) {
            $constraints = [new NotBlank(), new \Symfony\Component\Validator\Constraints\DateTime(format: 'Y-m-d')];
            $violations = Validation::createValidator()->validate($value, $constraints);
            foreach ($violations as $violation) {
                $this->context->buildViolation((string) $violation->getMessage())->addViolation();
            }
        }
    }
}