<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\Node\Expr\Variable;

/**
 * Carries the state shared by a whole parameter denormalization tree: a counter handing out unique
 * temporary variable names, and the models visited on the current branch, used to detect cycles.
 */
class DenormalizationContext
{
    private int $counter = 0;

    /**
     * @var list<string>
     */
    private array $visitedModels = [];

    public function nextVariable(): Variable
    {
        return new Variable(\sprintf('v%d', $this->counter++));
    }

    /**
     * @param list<string> $path
     *
     * @throws Exception
     */
    public function enterModel(string $name, array $path): void
    {
        if (\in_array($name, $this->visitedModels, true)) {
            throw new Exception('Recursive schemas are not supported for array and object parameters.', $path);
        }

        $this->visitedModels[] = $name;
    }

    public function leaveModel(): void
    {
        array_pop($this->visitedModels);
    }
}
