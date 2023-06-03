<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractController
{
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            throw new \Exception();
        }

        return $value;
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        return $value;
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            throw new \Exception();
        }

        if (!ctype_digit($value)) {
            throw new \RuntimeException();
        }

        return intval($value);
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!ctype_digit($value)) {
            throw new \RuntimeException();
        }

        return intval($value);
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            throw new \Exception();
        }

        if (!is_numeric($value)) {
            throw new \RuntimeException();
        }

        return floatval($value);
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new \RuntimeException();
        }

        return floatval($value);
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            throw new \Exception();
        }

        if (!in_array($value, ['true', 'false'], true)) {
            throw new \RuntimeException();
        }

        return ['true' => true, 'false' => false][$value];
    }

    /**
     * @throws \Exception
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
            default => throw new \Exception(),
        };

        $isset = $bag->has($name);
        $value = $bag->get($name);

        if (!$isset) {
            if ($required) {
                throw new \Exception();
            }

            $value = $default;
        }

        if ($value === null) {
            return null;
        }

        if (!in_array($value, ['true', 'false'], true)) {
            throw new \RuntimeException();
        }

        return ['true' => true, 'false' => false][$value];
    }

}
