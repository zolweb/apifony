<?php

namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
abstract class AbstractController
{
    public function __construct(protected readonly SerializerInterface $serializer, protected readonly ValidatorInterface $validator)
    {
    }
    /**
     * @param array<Constraint> $constraints
     *
     * @throws ParameterValidationException
     */
    public function validateParameter(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (count($violations) > 0) {
            throw new ParameterValidationException(array_map(static fn(ConstraintViolationInterface $violation) => $violation->getMessage(), iterator_to_array($violations)));
        }
    }
    /**
     * @param array<Constraint> $constraints
     *
     * @throws RequestBodyValidationException
     */
    public function validateRequestBody(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $path = $violation->getPropertyPath();
                if (!isset($errors[$path])) {
                    $errors[$path] = [];
                }
                $errors[$path][] = (string) $violation->getMessage();
            }
            throw new RequestBodyValidationException($errors);
        }
    }
}