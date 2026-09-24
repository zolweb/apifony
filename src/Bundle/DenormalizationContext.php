<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;

/**
 * Carries the state shared by a whole denormalization tree: which source the values come from, a
 * counter handing out unique temporary variable names, and the registry of the models met along
 * the way.
 *
 * The source matters because a query string leaf is always a string and must be converted, while a
 * JSON leaf already carries its type and must only be checked.
 */
class DenormalizationContext
{
    public const SOURCE_QUERY = 'Parameter';
    public const SOURCE_JSON = 'Json';

    private int $counter = 0;

    /**
     * @var array<string, ObjectType>
     */
    private array $models = [];

    public function __construct(
        private readonly string $source,
    ) {
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Wraps the statements denormalizing a value so that a null yields null instead. Only a JSON
     * document can carry a null: a query string has no notation for it.
     *
     * @param callable(Expr): list<Stmt> $inner
     *
     * @return list<Stmt>
     */
    public function wrapNullable(bool $nullable, Expr $source, Expr $target, callable $inner): array
    {
        if (!$nullable || $this->source !== self::SOURCE_JSON) {
            return $inner($source);
        }

        $value = $this->nextVariable();

        return [
            new Expression(new Assign($value, $source)),
            new Expression(new Assign($target, new ConstFetch(new Name('null')))),
            new If_(new NotIdentical($value, new ConstFetch(new Name('null'))), ['stmts' => $inner($value)]),
        ];
    }

    public function nextVariable(): Variable
    {
        return new Variable(\sprintf('v%d', $this->counter++));
    }

    /**
     * Restarts the temporary variable names, so that every generated method reads from $v0.
     */
    public function resetVariables(): void
    {
        $this->counter = 0;
    }

    /**
     * Records that a model is denormalized for this source, so that two schemas collapsing onto one
     * model name are caught rather than silently binding the second one's denormalizer to the
     * first. The name alone cannot tell them apart, so the specification location does.
     *
     * @throws Exception
     */
    public function registerModel(ObjectType $type): void
    {
        $name = $type->getName();

        if (isset($this->models[$name]) && $this->models[$name]->getSchemaPath() !== $type->getSchemaPath()) {
            throw new Exception(\sprintf('Two different schemas both map to the \'%s\' model.', $name), $type->getSchemaPath());
        }

        $this->models[$name] = $type;
    }

    /**
     * Emitting one method per model, rather than inlining the whole type tree at each use site, is
     * what lets a recursive schema produce recursive code instead of a generator that never
     * terminates.
     */
    public static function getModelMethodName(string $modelName, string $source): string
    {
        return \sprintf('denormalize%s%sValue', $modelName, $source);
    }
}
