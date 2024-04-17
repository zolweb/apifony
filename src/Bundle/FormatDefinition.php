<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Namespace_;

class FormatDefinition implements File
{
    public static function build(
        string $bundleNamespace,
        string $formatName,
    ): self {
        return new self(
            $bundleNamespace,
            $formatName,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $formatName,
    ) {
    }

    public function getInterfaceName(): string
    {
        return "{$this->formatName}Definition";
    }

    public function getFolder(): string
    {
        return 'src/Format';
    }

    public function getName(): string
    {
        return "{$this->formatName}Definition.php";
    }

    public function getTemplate(): string
    {
        return 'format-definition.php.twig';
    }

    public function getParametersRootName(): string
    {
        return 'definition';
    }

    public function hasNamespaceAst(): bool
    {
        return true;
    }

    public function getNamespaceAst(): Namespace_
    {
        $f = new BuilderFactory();

        $methodValidate = $f->method('validate');
        $methodValidate->makePublic();
        $methodValidate->addParam($f->param('value')->setType('mixed'));
        $methodValidate->setReturnType('array');
        $methodValidate->setDocComment(<<<'COMMENT'
            /**
             * @return string[]
             */
            COMMENT
        );

        $interface = $f->interface("{$this->formatName}Definition");
        $interface->addStmt($methodValidate);

        $namespace = $f->namespace("{$this->bundleNamespace}\Format");
        $namespace->addStmt($interface);

        return $namespace->getNode();
    }
}