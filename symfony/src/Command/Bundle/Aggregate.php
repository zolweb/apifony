<?php

namespace App\Command\Bundle;

use App\Command\OpenApi\Components;
use App\Command\OpenApi\Operation;
use function Symfony\Component\String\u;

class Aggregate
{
    /**
     * @param array<Operation> $operations
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $tag,
        array $operations,
        Components $components,
    ): self {
        $name = u($tag)->camel()->title();

        return new self(
            $handler = Handler::build(
                $bundleNamespace,
                $name,
                $actions = array_map(
                    static fn (Operation $operation) =>
                        Action::build($bundleNamespace, $name, $operation, $components),
                    $operations,
                ),
            ),
            Controller::build(
                $bundleNamespace,
                $name,
                $actions,
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
     * @return array<File>
     */
    public function getFiles(): array
    {
        $files = [
            $this->handler,
            $this->controller,
        ];

        foreach ($this->handler->getFiles() as $file) {
            $files[] = $file;
        }

        return $files;
    }
}