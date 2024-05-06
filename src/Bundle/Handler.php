<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Builder\Method;
use PhpParser\Builder\Use_;
use PhpParser\BuilderFactory;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;

class Handler implements File
{
    /**
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
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
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
     */
    private function __construct(
        private readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedModelNames,
    ) {
    }

    /**
     * @return array<File>
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

    public function getClassName(): string
    {
        return "{$this->aggregateName}Handler";
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

        // TODO Move in action and actioncase
        $interface = $f->interface("{$this->aggregateName}Handler")
            ->addStmts(array_merge(...array_map(
                static fn (Action $action): array => array_map(
                    static fn (ActionCase $actionCase): Method => $f->method($actionCase->getName())
                        ->makePublic()
                        ->addParams(array_merge(
                            array_map(
                                static fn (ActionParameter $param): Param => $param->asParam(),
                                $actionCase->getParameters(),
                            ),
                            $actionCase->hasRequestBodyPayloadParameter() ? [$f->param('requestBodyPayload')->setType($actionCase->getRequestBodyPayloadTypeName())] : [],
                        ))
                        ->setReturnType(new UnionType(array_map(
                            static fn (ActionResponse $response) => new Name($response->getClassName()),
                            $actionCase->getResponses(),
                        ))),
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
            new Declare_([new DeclareItem('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
