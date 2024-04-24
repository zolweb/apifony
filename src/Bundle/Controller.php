<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;

class Controller implements File
{
    /**
     * @param array<Action> $actions
     * @param array<string> $usedModelNames
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        Handler $handler,
        array $usedModelNames,
    ): self {
        # todo maybe import all formats and models and let php-cs-fixer remove unused
        $usedFormatConstraintNames = [];
        foreach ($actions as $action) {
            foreach ($action->getParameters() as $parameter) {
                foreach ($parameter->getConstraints() as $constraint) {
                    foreach ($constraint->getFormatConstraintClassNames() as $constraintName) {
                        $usedFormatConstraintNames[$constraintName] = true;
                    }
                }
            }
        }

        return new self(
            $handler,
            $actions,
            $bundleNamespace,
            $aggregateName,
            array_keys($usedFormatConstraintNames),
            $usedModelNames,
        );
    }

    /**
     * @param array<Action> $actions
     * @param array<string> $usedFormatConstraintNames
     * @param array<string> $usedModelNames
     */
    private function __construct(
        public readonly Handler $handler,
        public readonly array $actions,
        private readonly string $bundleNamespace,
        private readonly string $aggregateName,
        private readonly array $usedFormatConstraintNames,
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
    public function getUsedFormatConstraintNames(): array
    {
        return $this->usedFormatConstraintNames;
    }

    /**
     * @return array<string>
     */
    public function getUsedModelNames(): array
    {
        return $this->usedModelNames;
    }

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\Api\\{$this->aggregateName}";
    }

    public function getClassName(): string
    {
        return "{$this->aggregateName}Controller";
    }

    public function getFolder(): string
    {
        return "src/Api/{$this->aggregateName}";
    }

    public function getName(): string
    {
        return "{$this->aggregateName}Controller.php";
    }

    public function getTemplate(): string
    {
        return 'controller.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'controller';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $setHandlerMethod = $f->method('setHandler')
            ->addParam($f->param('handler')->setType($this->handler->getClassName()))
            ->setReturnType('void')
            ->makePublic()
            ->addStmt(new Assign($f->propertyFetch($f->var('this'), 'handler'), $f->var('handler')));

        $class = $f->class("{$this->aggregateName}Controller")
            ->extend('AbstractController')
            ->addStmt($f->property('handler')->setType($this->handler->getClassName())->makePrivate())
            ->addStmt($setHandlerMethod);

        foreach ($this->actions as $action) {
            $actionMethod = $f->method($action->getName())
                ->makePublic()
                ->addParam($f->param('request')->setType('Request'));

            foreach ($action->getParameters(['path']) as $parameter) {
                $actionMethod->addParam($f->param($parameter->getRawName())->setType($parameter->getPhpType()));
            }

            $actionMethod->setReturnType('Response')
                ->addStmt(new Expression(new Assign($f->var('errors'), $f->val([]))));

            foreach ($action->getParameters(['path']) as $parameter) {
                $actionMethod->addStmt(new Expression(new Assign($f->var($parameter->getVariableName()), $f->var($parameter->getRawName()))))
                    ->addStmt(new TryCatch([
                        new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                            $f->var($parameter->getVariableName()),
                            array_map(
                                static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                                $parameter->getConstraints(),
                            ),
                        ])),
                    ], [
                        new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), $f->propertyFetch($f->var('e'), 'messages'))),
                        ]),
                    ]));
            }

            foreach ($action->getParameters(['query', 'header', 'cookie']) as $parameter) {
                $actionMethod->addStmt(new Expression(new Assign($f->var($parameter->getVariableName()), $f->val($parameter->getInitValue()))))
                    -> addStmt(new TryCatch([
                        new Expression(new Assign($f->var($parameter->getVariableName()), $f->methodCall($f->var('this'), sprintf('get%s%sParameter', ucfirst($parameter->getPhpType()), $parameter->isNullable() ? 'OrNull' : ''), array_merge([$f->var('request'), $parameter->getRawName(), $parameter->getIn(), $parameter->isRequired()], $parameter->hasDefault() ? [$parameter->getDefault()] : [])))),
                        new Expression($f->methodCall($f->var('this'), 'validateParameter', [
                            $f->var($parameter->getVariableName()),
                            array_map(
                                static fn (Constraint $constraint): New_ => $constraint->getInstantiationAst(),
                                $parameter->getConstraints(),
                            ),
                        ])),
                    ], [
                        new Catch_([new Name('DenormalizationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), new Array_([$f->propertyFetch($f->var('e'), 'messages')]))),
                        ]),
                        new Catch_([new Name('ParameterValidationException')], $f->var('e'), [
                            new Expression(new Assign(new ArrayDimFetch(new ArrayDimFetch($f->var('errors'), $f->val($parameter->getIn())), $f->val($parameter->getRawName())), $f->propertyFetch($f->var('e'), 'messages'))),
                        ])
                    ]));
            }

            $class->addStmt($actionMethod);
        }

        $namespace = $f->namespace("{$this->bundleNamespace}\Api\\{$this->aggregateName}")
            ->addStmt($f->use("{$this->bundleNamespace}\Api\DenormalizationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\ParameterValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\RequestBodyValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\Api\AbstractController"))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\JsonResponse'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Response'))
            ->addStmt(new Use_([new UseItem(new Name('Symfony\Component\Validator\Constraints'), 'Assert')]));

        foreach ($this->usedFormatConstraintNames as $usedFormatConstraintName) {
            $namespace->addStmt(new Use_([new UseItem(new Name("{$this->bundleNamespace}\Format\\{$usedFormatConstraintName}"), "Assert{$usedFormatConstraintName}")]));
        }

        foreach ($this->usedModelNames as $usedModelName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\Model\\{$usedModelName}"));
        }

        $namespace->addStmt($class);

        return $namespace->getNode();
    }
}