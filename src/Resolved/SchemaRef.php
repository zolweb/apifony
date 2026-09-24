<?php

declare(strict_types=1);

namespace Zol\Apifony\Resolved;

/**
 * A schema slot as the specification writes it: what it points at, and whether it was written as a
 * '$ref'. The second half is what the generator cannot recover from the target alone. It decides
 * the namespace the class lands in, the name it takes, and whether a file is emitted for it at all,
 * so resolving a reference without carrying the fact that it was one loses information.
 *
 * Only schemas are wrapped this way. A parameter, a header, a request body and a response can also
 * be written as a reference, but nothing downstream ever asks: only a schema names a class. Those
 * slots therefore hold their target directly.
 *
 * A reference is created without its target and bound afterwards, once every components schema has
 * been built. That is what lets a recursive specification resolve at all: building the graph treats
 * a reference as a leaf and never follows one, so the walk is bounded by the parse tree, which is
 * finite and acyclic. Binding then closes the cycles.
 */
final class SchemaRef
{
    private ?Schema $target;

    /**
     * @param list<string> $path
     */
    private function __construct(
        public readonly bool $isReference,
        public readonly ?string $componentName,
        public readonly array $path,
        ?Schema $target,
    ) {
        $this->target = $target;
    }

    /**
     * A slot spelled out in place, which already holds its target.
     */
    public static function inline(Schema $target): self
    {
        return new self(false, null, $target->path, $target);
    }

    /**
     * @param list<string> $path
     */
    public static function reference(string $componentName, array $path): self
    {
        return new self(true, $componentName, $path, null);
    }

    /**
     * Binding is the Resolver's job and happens exactly once, before anything reads the graph.
     *
     * @internal
     */
    public function bind(Schema $target): void
    {
        if ($this->target !== null) {
            throw new \LogicException('A schema slot is bound once.');
        }

        $this->target = $target;
    }

    public function getTarget(): Schema
    {
        if ($this->target === null) {
            throw new \LogicException('A schema slot is read only once the resolver has bound it.');
        }

        return $this->target;
    }

    /**
     * The specification name of the components schema this slot points at, or null when the slot
     * is spelled out inline and therefore names nothing of its own.
     */
    public function getComponentName(): ?string
    {
        return $this->componentName;
    }
}
