<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\Node\Expr\Variable;

/**
 * Carries the state shared by a whole parameter denormalization tree: a counter handing out unique
 * temporary variable names, and the registry of the models met along the way.
 */
class DenormalizationContext
{
    private int $counter = 0;

    /**
     * @var array<string, ObjectType>
     */
    private array $models = [];

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
     * Registers a model and returns the name of the controller method denormalizing it. Emitting
     * one method per model, rather than inlining the whole type tree at each use site, is what
     * lets a recursive schema produce recursive code instead of a generator that never terminates.
     */
    public function registerModel(ObjectType $type): string
    {
        $this->models[$type->getName()] = $type;

        return self::getModelMethodName($type->getName());
    }

    public static function getModelMethodName(string $modelName): string
    {
        return \sprintf('denormalize%sParameterValue', $modelName);
    }

    /**
     * @return array<string, ObjectType>
     */
    public function getModels(): array
    {
        return $this->models;
    }
}
