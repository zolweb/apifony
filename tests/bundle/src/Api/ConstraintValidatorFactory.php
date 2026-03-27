<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
final class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /** @var array<string, ConstraintValidatorInterface> */
    private array $validators = [];
    public function addValidator(ConstraintValidatorInterface $validator): void
    {
        $this->validators[$validator::class] = $validator;
    }
    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();
        if (!isset($this->validators[$name])) {
            $validator = new $name();
            if (!$validator instanceof ConstraintValidatorInterface) {
                throw new \RuntimeException();
            }
            $this->validators[$name] = $validator;
        }
        return $this->validators[$name];
    }
}