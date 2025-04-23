<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Builder\Use_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\PrettyPrinter\Standard;

class Handler implements File
{
    /**
     * @param list<Action> $actions
     * @param list<string> $usedModelNames
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        array $usedModelNames,
    ): self {
        return new self(
            $actions,
            $bundleNamespace,
            $aggregateName,
            $usedModelNames,
        );
    }

    /**
     * @param list<Action> $actions
     * @param list<string> $usedModelNames
     */
    private function __construct(
        private readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedModelNames,
    ) {
    }

    /**
     * @return list<File>
     */
    public function getFiles(): array
    {
        $files = [];

        foreach ($this->actions as $action) {
            foreach ($action->getFiles() as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->aggregateName}Handler.php";
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $interface = $f->interface("{$this->aggregateName}Handler")
            ->addStmts(array_merge(...array_map(
                static fn (Action $action): array => array_map(
                    static fn (ActionCase $actionCase): ClassMethod => $actionCase->getHandlerMethod(),
                    $action->getCases(),
                ),
                $this->actions,
            )))
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api\\{$this->aggregateName}")
            ->addStmts(array_map(
                fn (string $modelName): Use_ => $f->use("{$this->bundleNamespace}\\Model\\{$modelName}"),
                $this->usedModelNames,
            ))
            ->addStmt($interface)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
