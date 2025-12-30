<?php

declare(strict_types=1);

namespace Zol\Apifony;

abstract class Narrow
{
    public static function int(mixed $value): int
    {
        if (!\is_int($value)) {
            throw new \RuntimeException(\sprintf('Integer type expected, %s given.', \gettype($value)));
        }

        return $value;
    }

    public static function stringOrNull(mixed $value): ?string
    {
        if (!\is_string($value) && $value !== null) {
            throw new \RuntimeException(\sprintf('Integer type expected, %s given.', \gettype($value)));
        }

        return $value;
    }
}
