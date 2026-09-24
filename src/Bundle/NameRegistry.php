<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

/**
 * The single place deciding what a generated name is allowed to be.
 *
 * Naming decides what a name is called, and the conversion is lossy: distinct specification names
 * can collapse onto one identifier. Until now each caller that cared guarded its own scope, with
 * its own key, its own lifetime and its own blind spot. A name is only ever meaningful inside a
 * scope — a namespace, a class body, a signature, a directory — so this holds one table per scope,
 * and every generated identifier is claimed from it before it is used. Validity and uniqueness are
 * both properties of entering a scope, so a claim checks both.
 *
 * Three properties make a mutable registry safe in an otherwise immutable design. It is monotonic:
 * claims are only ever added, there is no release and no rollback. It is invisible to the output:
 * nothing reads it while emitting, so it decides whether generation continues and never what is
 * generated. And it is short lived, belonging to one Bundle::build call.
 *
 * Class, file and method names are folded to lower case before being compared, because neither PHP
 * nor a case insensitive filesystem tells 'Abc' from 'ABC'. The spelling the claimant used is kept
 * for the message.
 */
final class NameRegistry
{
    /**
     * @var array<string, array<string, array{origin: Origin, display: string}>>
     */
    private array $scopes = [];

    /**
     * @throws Exception
     */
    public function claimBundle(string $name, Origin $origin): void
    {
        $this->assertIdentifier($name, $origin);
        $this->claim('bundle', 'bundle', strtolower($name), $name, $origin);
    }

    /**
     * @throws Exception
     */
    public function claimAggregate(string $name, Origin $origin): void
    {
        $this->assertIdentifier($name, $origin);
        $this->claim('aggregate', 'aggregate', strtolower($name), $name, $origin);
    }

    /**
     * @throws Exception
     */
    public function claimClass(string $namespace, string $className, Origin $origin): void
    {
        $this->assertIdentifier($className, $origin);
        $this->claim('class', 'class', strtolower("{$namespace}\\{$className}"), "{$namespace}\\{$className}", $origin);
    }

    /**
     * @throws Exception
     */
    public function claimMethod(string $classFqn, string $name, Origin $origin): void
    {
        $this->assertIdentifier($name, $origin);
        $this->claim("method:{$classFqn}", 'method', strtolower($name), "{$classFqn}::{$name}", $origin);
    }

    /**
     * A name reaching PHP as a variable. Variable names are case sensitive, so nothing is folded.
     *
     * @throws Exception
     */
    public function claimArgument(string $classFqn, string $method, string $name, Origin $origin): void
    {
        $this->assertIdentifier($name, $origin);
        $this->claim("argument:{$classFqn}::{$method}", 'handler argument', $name, "\${$name}", $origin);
    }

    /**
     * The short name a generated file binds through a use statement. Two classes may legitimately
     * share a short name in different namespaces; what cannot happen is one file importing both,
     * since the second import would silently bind the first one's name to the wrong class.
     *
     * @throws Exception
     */
    public function claimImport(string $file, string $shortName, Origin $origin): void
    {
        $this->claim("import:{$file}", \sprintf('import of \'%s\'', $file), strtolower($shortName), $shortName, $origin);
    }

    /**
     * @throws Exception
     */
    private function assertIdentifier(string $identifier, Origin $origin): void
    {
        if (!Naming::isIdentifier($identifier)) {
            throw new Exception(\sprintf('%s produces \'%s\', which is not a valid PHP identifier.', ucfirst($origin->description), $identifier), $origin->path);
        }
    }

    /**
     * @throws Exception
     */
    private function claim(string $scope, string $label, string $key, string $display, Origin $origin): void
    {
        $held = $this->scopes[$scope][$key] ?? null;

        if ($held === null) {
            $this->scopes[$scope][$key] = ['origin' => $origin, 'display' => $display];

            return;
        }

        // One thing reaching one name twice, which is how a shared or a recursive schema is met, is
        // a single claim rather than a clash.
        if ($held['origin']->identity === $origin->identity) {
            return;
        }

        throw new Exception(\sprintf('%s and %s both map to the %s \'%s\'.', ucfirst($held['origin']->description), $origin->description, $label, $held['display']), $origin->path);
    }
}
