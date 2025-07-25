<?php

declare (strict_types=1);
namespace Zol\Apifony\Tests\TestOpenApiServer\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
abstract class AbstractController
{
    public function __construct(protected readonly DeserializerInterface $deserializer, protected readonly ValidatorInterface $validator)
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        return $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        return $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
        }
        return (int) $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!ctype_digit($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be an integer.");
        }
        return (int) $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
        }
        return (float) $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        $absValue = $value;
        if (str_starts_with($value, '-')) {
            $absValue = substr($value, 1);
        }
        if (!is_numeric($absValue)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a numeric.");
        }
        return (float) $value;
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            if ($default === null) {
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
            }
            return $default;
        }
        if ($value === null) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must not be null.");
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
        }
        return ['true' => true, 'false' => false][$value];
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
                throw new DenormalizationException("Parameter '{$name}' in '{$in}' is required.");
            }
            return $default;
        }
        if ($value === null) {
            return null;
        }
        if (!\in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '{$name}' in '{$in}' must be a boolean.");
        }
        return ['true' => true, 'false' => false][$value];
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringRequestBody(Request $request, ?string $default = null): string
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_string($value)) {
            throw new DenormalizationException('Request body must be a string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getStringOrNullRequestBody(Request $request, ?string $default = null): ?string
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_string($value)) {
            throw new DenormalizationException('Request body must be a string.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntRequestBody(Request $request, ?int $default = null): int
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_int($value)) {
            throw new DenormalizationException('Request body must be an integer.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getIntOrNullRequestBody(Request $request, ?int $default = null): ?int
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_int($value)) {
            throw new DenormalizationException('Request body must be an integer.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatRequestBody(Request $request, ?float $default = null): float
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException('Request body must be a numeric.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getFloatOrNullRequestBody(Request $request, ?float $default = null): ?float
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_int($value) && !\is_float($value)) {
            throw new DenormalizationException('Request body must be a numeric.');
        }
        return (float) $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolRequestBody(Request $request, ?bool $default = null): bool
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        $value = json_decode($value, true);
        if (!\is_bool($value)) {
            throw new DenormalizationException('Request body must be a boolean.');
        }
        return $value;
    }
    /**
     * @throws DenormalizationException
     */
    public function getBoolOrNullRequestBody(Request $request, ?bool $default = null): ?bool
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        $value = json_decode($value, true);
        if ($value === null) {
            return null;
        }
        if (!\is_bool($value)) {
            throw new DenormalizationException('Request body must be a boolean.');
        }
        return $value;
    }
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param ?T $default
     *
     * @return T
     *
     * @throws DenormalizationException
     */
    public function getObjectRequestBody(Request $request, string $class, ?object $default = null): object
    {
        $value = $request->getContent();
        if ($value === '') {
            if ($default === null) {
                throw new DenormalizationException('Request body must not be null.');
            }
            return $default;
        }
        try {
            return $this->deserializer->deserialize($value, $class);
        } catch (ExceptionInterface|\TypeError $e) {
            throw new DenormalizationException("Request body could not be deserialized: {$e->getMessage()}");
        }
    }
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param ?T $default
     *
     * @return ?T
     *
     * @throws DenormalizationException
     */
    public function getObjectOrNullRequestBody(Request $request, string $class, ?object $default = null): ?object
    {
        $value = $request->getContent();
        if ($value === '') {
            return $default;
        }
        if ($value === 'null') {
            return null;
        }
        try {
            return $this->deserializer->deserialize($value, $class);
        } catch (ExceptionInterface|\TypeError $e) {
            throw new DenormalizationException("Request body could not be deserialized: {$e->getMessage()}");
        }
    }
    /**
     * @param list<Constraint> $constraints
     *
     * @throws ParameterValidationException
     */
    public function validateParameter(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (\count($violations) > 0) {
            throw new ParameterValidationException(array_map(static fn(ConstraintViolationInterface $violation) => (string) $violation->getMessage(), iterator_to_array($violations)));
        }
    }
    /**
     * @param list<Constraint> $constraints
     *
     * @throws RequestBodyValidationException
     */
    public function validateRequestBody(mixed $value, array $constraints): void
    {
        $violations = $this->validator->validate($value, $constraints);
        if (\count($violations) > 0) {
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