<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\PrettyPrinter\Standard;

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

    public function getContent(): string
    {
        $f = new BuilderFactory();

        $methodValidate = $f->method('validate')
            ->makePublic()
            ->addParam($f->param('value')->setType('mixed'))
            ->setReturnType('array')
            ->setDocComment(<<<'COMMENT'
                /**
                 * @return string[]
                 */
                COMMENT
            )
        ;

        $interface = $f->interface("{$this->formatName}Definition")
            ->addStmt($methodValidate)
        ;

        $namespace = $f->namespace("{$this->bundleNamespace}\\Format")
            ->addStmt($interface)
        ;

        return (new Standard())->prettyPrintFile([
            new Declare_([new DeclareItem('strict_types', $f->val(1))]),
            $namespace->getNode(),
        ]);
    }
}
