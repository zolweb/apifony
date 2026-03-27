<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
class TimeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (\is_string($value)) {
            $normalizedValue = str_replace(['z'], ['Z'], $value);
            $allowedFormats = ['!H:i:sP', '!H:i:s\Z', '!H:i:s.uP', '!H:i:s.u\Z'];
            foreach ($allowedFormats as $allowedFormat) {
                $dateTime = \DateTimeImmutable::createFromFormat($allowedFormat, $normalizedValue);
                $errors = \DateTimeImmutable::getLastErrors();
                if ($dateTime !== false && ($errors === false || $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    return;
                }
            }
            $this->context->buildViolation('This is not a valid time format according to RFC 3339.')->addViolation();
        }
    }
}