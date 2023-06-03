<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractController
{
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws DenormalizationException
     */
    public function getStringParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?string $default = null,
    ): string {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            throw new DenormalizationException("Parameter '$name' in '$in' must not be null.");
        }

        return $value;
    }

    /**
     * @throws DenormalizationException
     */
    public function getStringOrNullParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?string $default = null,
    ): ?string {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        return $value;
    }

    /**
     * @throws DenormalizationException
     */
    public function getIntParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?int $default = null,
    ): int {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            throw new DenormalizationException("Parameter '$name' in '$in' must not be null.");
        }

        if (!ctype_digit($value)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be an integer.");
        }

        return intval($value);
    }

    /**
     * @throws DenormalizationException
     */
    public function getIntOrNullParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?int $default = null,
    ): ?int {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!ctype_digit($value)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be an integer.");
        }

        return intval($value);
    }

    /**
     * @throws DenormalizationException
     */
    public function getFloatParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?float $default = null,
    ): float {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            throw new DenormalizationException("Parameter '$name' in '$in' must not be null.");
        }

        if (!is_numeric($value)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be a numeric.");
        }

        return floatval($value);
    }

    /**
     * @throws DenormalizationException
     */
    public function getFloatOrNullParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?float $default = null,
    ): ?float {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be a numeric.");
        }

        return floatval($value);
    }

    /**
     * @throws DenormalizationException
     */
    public function getBoolParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?bool $default = null,
    ): bool {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            throw new DenormalizationException("Parameter '$name' in '$in' must not be null.");
        }

        if (!in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be a boolean.");
        }

        return ['true' => true, 'false' => false][$value];
    }

    /**
     * @throws DenormalizationException
     */
    public function getBoolOrNullParameter(
        Request $request,
        string $name,
        string $in,
        bool $required,
        ?bool $default = null,
    ): ?bool {
        $bag = match ($in) {
            'query' => $request->query,
            'header' => $request->headers,
            'cookie' => $request->cookies,
            default => throw new RuntimeException('Invalid parameter location.'),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new DenormalizationException("Parameter '$name' in '$in' is required.");
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!in_array($value, ['true', 'false'], true)) {
            throw new DenormalizationException("Parameter '$name' in '$in' must be a boolean.");
        }

        return ['true' => true, 'false' => false][$value];
    }

    /**
     * @param array<Constraint> $constraints
     * @param array<string, array<string, array<string>>> $errors
     */
    public function validateParameter(
        string $name,
        string $in,
        mixed $value,
        array $constraints,
        array& $errors,
    ): void {
        $violations = $this->validator->validate($value, $constraints);

        if (count($violations) > 0) {
            $errors[$in][$name] = array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getMessage(),
                iterator_to_array($violations),
            );
        }
    }
}
