<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
class DateTimeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (\is_string($value)) {
            $normalizedValue = str_replace(['t', 'z'], ['T', 'Z'], $value);
            $allowedFormats = ['!Y-m-d\TH:i:sP', '!Y-m-d\TH:i:s\Z', '!Y-m-d\TH:i:s.uP', '!Y-m-d\TH:i:s.u\Z'];
            foreach ($allowedFormats as $allowedFormat) {
                $dateTime = \DateTimeImmutable::createFromFormat($allowedFormat, $normalizedValue);
                $errors = \DateTimeImmutable::getLastErrors();
                if ($dateTime !== false && ($errors === false || $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    return;
                }
            }
            $this->context->buildViolation('This is not a valid date time format according to RFC 3339.')->addViolation();
        }
    }
}