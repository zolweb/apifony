<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;

class Aggregate
{
    /**
     * @param array<Operation> $operations
     */
    public static function build(
        string $bundleNamespace,
        string $name,
        array $operations,
        AbstractController $abstractController,
        Components $components,
    ): self {
        return new self(
            $handler = Handler::build(
                $bundleNamespace,
                $name,
                $actions = array_map(
                    static fn (Operation $operation) => Action::build($operation, $components),
                    $operations,
                ),
            ),
            Controller::build(
                $bundleNamespace,
                $name,
                $actions,
                $abstractController,
                $handler,
            ),
        );
    }

    private function __construct(
        private readonly Handler $handler,
        private readonly Controller $controller,
    ) {
    }

    /**
     * @param array<File> $files
     */
    public function addFiles(array& $files): void
    {
        $files[] = $this->handler;
        $files[] = $this->controller;
    }
}