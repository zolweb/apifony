<?php

declare(strict_types=1);

namespace Zol\Apifony\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
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

        $validator = match ($rawName) {
            'email' => EmailValidator::build($bundleNamespace),
            'uuid' => UuidValidator::build($bundleNamespace),
            'date-time' => DateTimeValidator::build($bundleNamespace),
            'date' => DateValidator::build($bundleNamespace),
            'time' => TimeValidator::build($bundleNamespace),
            default => FormatValidator::build($bundleNamespace, $name),
        };

        return new self(
            $bundleNamespace,
            $rawName,
            $name,
            $validator instanceof FormatValidator ? FormatDefinition::build($bundleNamespace, $name) : null,
            FormatConstraint::build($bundleNamespace, $name),
            $validator,
        );
    }

    private function __construct(
        private readonly string $bundleNamespace,
        private readonly string $rawName,
        private readonly string $name,
        private readonly ?FormatDefinition $definition,
        private readonly FormatConstraint $constraint,
        private readonly FormatValidator|EmailValidator|UuidValidator|DateTimeValidator|DateValidator|TimeValidator $validator,
    ) {
    }

    public function getValidator(): FormatValidator|EmailValidator|UuidValidator|DateTimeValidator|DateValidator|TimeValidator
    {
        return $this->validator;
    }

    /**
     * @return list<File>
     */
    public function getFiles(): array
    {
        $files = [
            $this->constraint,
            $this->validator,
        ];

        if ($this->definition !== null) {
            $files[] = $this->definition;
        }

        return $files;
    }

    public function getCase(): Case_
    {
        $f = new BuilderFactory();

        return new Case_($f->val($this->rawName), [
            new Expression($f->methodCall($f->methodCall($f->var('container'), 'findDefinition', ["{$this->bundleNamespace}\\Format\\{$this->name}Validator"]), 'addMethodCall', [$f->val('setFormatDefinition'), new Array_([new ArrayItem($f->new('Reference', [$f->var('id')]))], ['kind' => Array_::KIND_SHORT])])),
            new Break_(),
        ]);
    }
}
