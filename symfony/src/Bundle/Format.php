<?php

namespace Zol\Ogen\Bundle;

use function Symfony\Component\String\u;

class Format
{
    public static function build(
        string $bundleNamespace,
        string $rawName,
    ): self {
        $name = u($rawName)->camel()->title();

        return new self(
            $definition = FormatDefinition::build($bundleNamespace, $name),
            FormatConstraint::build($bundleNamespace, $name),
            FormatValidator::build($bundleNamespace, $name, $definition),
        );
    }

    private function __construct(
        private readonly FormatDefinition $definition,
        private readonly FormatConstraint $constraint,
        private readonly FormatValidator $validator,
    ) {
    }

    public function getConstraint(): FormatConstraint
    {
        return $this->constraint;
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
}