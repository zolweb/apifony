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
     * @throws DenormalizationException
     */
    public function getStringParameter(Request $request, string $name, string $in, bool $required, ?string $default = null): string
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringOrNullParameter(Request $request, string $name, string $in, bool $required, ?string $default = null): ?string
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntParameter(Request $request, string $name, string $in, bool $required, ?int $default = null): int
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntOrNullParameter(Request $request, string $name, string $in, bool $required, ?int $default = null): ?int
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatParameter(Request $request, string $name, string $in, bool $required, ?float $default = null): float
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatOrNullParameter(Request $request, string $name, string $in, bool $required, ?float $default = null): ?float
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolParameter(Request $request, string $name, string $in, bool $required, ?bool $default = null): bool
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolOrNullParameter(Request $request, string $name, string $in, bool $required, ?bool $default = null): ?bool
    {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new \RuntimeException('Invalid parameter location.'),
        };
        $isset = $bag->has($name);
        $value = $bag->get($name);
        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter {$name} in {$in} is required.");
            }
        }
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