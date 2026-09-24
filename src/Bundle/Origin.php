<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

/**
 * Who claimed a name, where that is written in the specification, and what makes the claim the
 * same claim when it is made twice.
 *
 * A name is reached more than once on purpose: a component schema referenced from several places,
 * a recursive one reached through itself. Those are one claim repeated, not a collision, and the
 * name alone cannot tell them apart from two different schemas that happen to collapse onto it.
 * The specification location can, so it is what identity is made of.
 */
final class Origin
{
    /**
     * @param list<string> $path
     */
    private function __construct(
        public readonly string $description,
        public readonly array $path,
        public readonly string $identity,
    ) {
    }

    /**
     * Something the specification declares: an operation, a schema, a parameter.
     *
     * Identity is the kind, the name as written, and the location, all three. The location alone
     * would not do: a format is declared inline wherever it is used and has no location of its
     * own, so two different formats would look like one thing claimed twice.
     *
     * @param list<string> $path
     */
    public static function spec(string $kind, string $rawName, array $path): self
    {
        return new self(
            \sprintf('%s \'%s\'', $kind, $rawName),
            $path,
            \sprintf("%s\0%s\0%s", $kind, $rawName, implode('/', $path)),
        );
    }
}
