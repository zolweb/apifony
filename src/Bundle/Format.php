<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Expression;

use function Symfony\Component\String\u;

class Format
{
    public static function build(
        string $bundleNamespace,
        string $rawName,
    ): self {
        $name = u($rawName)->camel()->title()->toString();

        return new self(
            $bundleNamespace,
            $rawName,
            $name,
            FormatDefinition::build($bundleNamespace, $name),
            FormatConstraint::build($bundleNamespace, $name),
            FormatValidator::build($bundleNamespace, $name),
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $rawName,
        private readonly string $name,
        private readonly FormatDefinition $definition,
        private readonly FormatConstraint $constraint,
        private readonly FormatValidator $validator,
    ) {
    }

    public function getValidator(): FormatValidator
    {
        return $this->validator;
    }

    /**
     * @return array<File>
     */
    public function getFiles(): array
    {
        return [
            $this->definition,
            $this->constraint,
            $this->validator,
        ];
    }

    public function getCase(): Case_
    {
        $f = new BuilderFactory();

        return new Case_($f->val($this->rawName), [
            new Expression($f->methodCall($f->methodCall($f->var('container'), 'findDefinition', ["{$this->bundleNamespace}\\Format\\{$this->name}Validator"]), 'addMethodCall', [$f->val('setFormatDefinition'), new Array_([new \PhpParser\Node\ArrayItem($f->new('Reference', [$f->var('id')]))])])),
            new Break_(),
        ]);
    }
}
