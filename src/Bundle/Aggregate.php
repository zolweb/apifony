<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use Zol\Ogen\OpenApi\Components;
use Zol\Ogen\OpenApi\Operation;

use function Symfony\Component\String\u;

class Aggregate
{
    /**
     * @param array<array{route: string, method: string, operation: Operation}> $operations
     *
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $tag,
        array $operations,
        ?Components $components,
    ): self {
        $name = u($tag)->camel()->title()->toString();

        $actions = [];
        foreach ($operations as $operation) {
            $actions[] = Action::build(
                $bundleNamespace,
                $name,
                $operation['route'],
                $operation['method'],
                $operation['operation'],
                $components,
            );
        }

        $usedModelNames = [];
        foreach ($actions as $action) {
            foreach ($action->getRequestBodies() as $requestBody) {
                if ($requestBody->getUsedModelName() !== null) {
                    $usedModelNames[$requestBody->getUsedModelName()] = true;
                }
            }
            foreach ($action->getCases() as $case) {
                foreach ($case->getResponses() as $response) {
                    if ($response->getUsedModelName() !== null) {
                        $usedModelNames[$response->getUsedModelName()] = true;
                    }
                }
            }
        }
        $usedModelNames = array_keys($usedModelNames);

        return new self(
            $name,
            $handler = Handler::build($bundleNamespace, $name, $actions, $usedModelNames),
            Controller::build($bundleNamespace, $name, $actions, $handler, $usedModelNames),
        );
    }

    private function __construct(
        private readonly string $name,
        private readonly Handler $handler,
        private readonly Controller $controller,
    ) {
    }

    public function getTag(): string
    {
        return u($this->name)->snake()->toString();
    }

    public function getController(): Controller
    {
        return $this->controller;
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
