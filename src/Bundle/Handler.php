<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\Builder\Use_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
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
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * @return array<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
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

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\\Api\\{$this->aggregateName}";
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

    public function getTemplate(): string
    {
        return 'handler.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'handler';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $interface = $f->interface("{$this->aggregateName}Handler")
            ->addStmts(array_merge(...array_map(
                static fn (Action $action): array => array_map(
                    static fn (ActionCase $actionCase): Method => $f->method($actionCase->getName())
                        ->makePublic()
                        ->addParams(array_merge(
                            array_map(
                                static fn (ActionParameter $param): Param => $f->param($param->getVariableName())->setType(sprintf('%s%s', $param->isNullable() ? '?' : '', $param->getPhpType())),
                                $actionCase->getParameters(),
                            ),
                            $actionCase->hasRequestBodyPayloadParameter() ? [$f->param('requestBodyPayload')->setType($actionCase->getRequestBodyPayloadParameterPhpType())] : [],
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

        return (new Standard())->prettyPrintFile([$namespace->getNode()]);
    }
}
