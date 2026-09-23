<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use function Symfony\Component\String\u;

/**
 * The single place turning a name written in the specification into a PHP identifier.
 *
 * The conversion is lossy: everything that is neither a letter nor a digit is dropped, so distinct
 * specification names can collapse onto the same identifier. Callers are responsible for detecting
 * those collisions; see the registries that guard each scope.
 */
class Naming
{
    /**
     * A PascalCase class name.
     */
    public static function forClass(string $name): string
    {
        return u($name)->camel()->title()->toString();
    }

    /**
     * A camelCase method, property or variable name.
     */
    public static function forMember(string $name): string
    {
        return u($name)->camel()->toString();
    }

    /**
     * A snake_case service id or route name.
     */
    public static function forServiceId(string $name): string
    {
        return u($name)->snake()->toString();
    }
}
