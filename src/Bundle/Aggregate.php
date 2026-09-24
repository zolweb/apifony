<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Use_;
use Zol\Apifony\Resolved\Operation;

class Aggregate
{
    /**
     * @throws Exception
     */
    public static function build(
        string $bundleNamespace,
        string $bundleName,
        string $route,
        string $method,
        Operation $operation,
    ): self {
        $name = Naming::forClass($operation->operationId);
        Naming::assertIdentifier($name, \sprintf('Operation \'%s\'', $operation->operationId), $operation->path);

        $action = Action::build(
            $bundleNamespace,
            $name,
            $route,
            $method,
            $operation,
        );

        $usedModelNames = [];
        if ($action->getRequestBody() !== null) {
            $usedModelName = $action->getRequestBody()->getUsedModelName();
            if ($usedModelName !== null) {
                $usedModelNames[$usedModelName] = true;
            }
        }
        foreach ($action->getResponses() as $response) {
            $usedModelName = $response->getUsedModelName();
            if ($usedModelName !== null) {
                $usedModelNames[$usedModelName] = true;
            }
        }
        foreach ($action->getParameters() as $parameter) {
            foreach ($parameter->getUsedModelNames() as $usedModelName) {
                $usedModelNames[$usedModelName] = true;
            }
        }
        $usedModelNames = array_keys($usedModelNames);

        return new self(
            $name,
            $handler = Handler::build($bundleNamespace, $name, $action, $usedModelNames),
            Controller::build($bundleNamespace, $name, $action, $handler, $usedModelNames),
            $bundleName,
        );
    }

    private function __construct(
        private readonly string $name,
        private readonly Handler $handler,
        private readonly Controller $controller,
        private readonly string $bundleName,
    ) {
    }

    /**
     * @throws Exception
     */
    public function registerDenormalizationModels(DenormalizationContext $queryContext, DenormalizationContext $jsonContext): void
    {
        $this->controller->action->registerDenormalizationModels($queryContext, $jsonContext);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @return list<File>
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

    public function getCase(): Case_
    {
        $f = new BuilderFactory();

        return new Case_($f->val(Naming::forServiceId($this->name)), [
            new Expression($f->methodCall($f->methodCall($f->var('container'), 'findDefinition', [\sprintf('%s\%s', $this->controller->getNamespace(), $this->controller->getClassName())]), 'addMethodCall', [$f->val('setHandler'), new Array_([new ArrayItem($f->new('Reference', [$f->var('id')]))], ['kind' => Array_::KIND_SHORT])])),
            new Break_(),
        ]);
    }

    public function getUse(): Use_
    {
        $f = new BuilderFactory();

        return $f->use(\sprintf('%s\%s', $this->handler->getNamespace(), $this->handler->getClassName()))->getNode();
    }

    public function getAutoconfiguration(): Expression
    {
        $f = new BuilderFactory();

        return new Expression(
            $f->methodCall(
                $f->methodCall($f->var('container'), 'registerForAutoconfiguration', [$f->classConstFetch($this->handler->getClassName(), 'class')]),
                'addTag',
                [
                    \sprintf('%s.handler', Naming::forServiceId($this->bundleName)),
                    new Array_([new ArrayItem($f->val(Naming::forServiceId($this->name)), $f->val('controller'))], ['kind' => Array_::KIND_SHORT]),
                ],
            ),
        );
    }
}
