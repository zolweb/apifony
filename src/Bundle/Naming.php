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
     * PHP allows bytes >= 0x80 in identifiers, so an accented name is fine; a leading digit is not.
     */
    private const IDENTIFIER_PATTERN = '/^[A-Za-z_\x80-\xFF][A-Za-z0-9_\x80-\xFF]*$/';

    /**
     * Guards the names that reach PHP as identifiers. The conversion cannot fix every input: a name
     * made only of punctuation comes out empty, and one starting with a digit stays that way.
     *
     * @param list<string> $path
     *
     * @throws Exception
     */
    public static function assertIdentifier(string $identifier, string $subject, array $path): void
    {
        if (preg_match(self::IDENTIFIER_PATTERN, $identifier) !== 1) {
            throw new Exception(\sprintf('%s produces \'%s\', which is not a valid PHP identifier.', $subject, $identifier), $path);
        }
    }

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
