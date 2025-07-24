<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\PrettyPrinter\Standard;

class Controller implements File
{
    /**
     * @param list<Action> $actions
     * @param list<string> $usedModelNames
     */
    public static function build(
        string $bundleNamespace,
        string $aggregateName,
        array $actions,
        Handler $handler,
        array $usedModelNames,
    ): self {
        // todo maybe import all formats and models and let php-cs-fixer remove unused
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
     * @param list<Action> $actions
     * @param list<string> $usedFormatConstraintNames
     * @param list<string> $usedModelNames
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

    public function getNamespace(): string
    {
        return "{$this->bundleNamespace}\\Api\\{$this->aggregateName}";
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

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $setHandlerMethod = $f->method('setHandler')
            ->addParam($f->param('handler')->setType("{$this->aggregateName}Handler"))
            ->setReturnType('void')
            ->makePublic()
            ->addStmt(new Assign($f->propertyFetch($f->var('this'), 'handler'), $f->var('handler')))
        ;

        $class = $f->class("{$this->aggregateName}Controller")
            ->extend('AbstractController')
            ->addStmt($f->property('handler')->setType("{$this->aggregateName}Handler")->makePrivate())
            ->addStmt($setHandlerMethod)
        ;

        foreach ($this->actions as $action) {
            $class->addStmt($action->getClassMethod());
        }

        $namespace = $f->namespace("{$this->bundleNamespace}\\Api\\{$this->aggregateName}")
            ->addStmt($f->use("{$this->bundleNamespace}\\Api\\DenormalizationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\\Api\\ParameterValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\\Api\\RequestBodyValidationException"))
            ->addStmt($f->use("{$this->bundleNamespace}\\Api\\AbstractController"))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\JsonResponse'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Request'))
            ->addStmt($f->use('Symfony\Component\HttpFoundation\Response'))
            ->addStmt(new Use_([new UseUse(new Name('Symfony\Component\Validator\Constraints'), 'Assert')]))
        ;

        foreach ($this->usedFormatConstraintNames as $usedFormatConstraintName) {
            $namespace->addStmt(new Use_([new UseUse(new Name("{$this->bundleNamespace}\\Format\\{$usedFormatConstraintName}"), "Assert{$usedFormatConstraintName}")]));
        }

        foreach ($this->usedModelNames as $usedModelName) {
            $namespace->addStmt($f->use("{$this->bundleNamespace}\\Model\\{$usedModelName}"));
        }

        $namespace->addStmt($class);

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareDeclare('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
