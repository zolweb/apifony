<?php

declare(strict_types=1);

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;

use function Symfony\Component\String\u;

class Constraint
{
    /**
     * @param array<string, string|int|float|bool|array<string|int|float|bool|self|null>|null> $parameters
     */
    public function __construct(
        private readonly string $name,
        private readonly array $parameters,
        private readonly ?string $formatName = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return list<string>
     */
    public function getFormatConstraintClassNames(): array
    {
        $formatConstraintNames = [];

        if ($this->formatName !== null) {
            $formatConstraintNames[] = (string) u($this->formatName)->camel()->title();
        }

        foreach ($this->parameters as $parameter) {
            if (\is_array($parameter)) {
                foreach ($parameter as $value) {
                    if ($value instanceof self && $value->formatName !== null) {
                        $formatConstraintNames[] = (string) u($value->formatName)->camel()->title();
                    }
                }
            }
        }

        return $formatConstraintNames;
    }

    public function getInstantiationAst(): New_
    {
        $f = new BuilderFactory();

        return $f->new(
            $this->name,
            $this->getArgumentsAst(),
        );
    }

    public function getAttributeAst(): Attribute
    {
        $f = new BuilderFactory();

        return $f->attribute(
            $this->name,
            $this->getArgumentsAst(),
        );
    }

    /**
     * @return Arg[]
     */
    public function getArgumentsAst(): array
    {
        $f = new BuilderFactory();

        return array_map(
            fn (string $parameterName) => new Arg(
                match (true) {
                    // todo s'occuper d'ajouter les / des regex dans une boucle en amont (constucteur ?)
                    \is_string($this->parameters[$parameterName]) && $this->name === 'Assert\Regex' && $parameterName === 'pattern' => $f->val("/{$this->parameters[$parameterName]}/"),
                    \is_array($this->parameters[$parameterName]) => new Array_(
                        array_map(
                            static fn ($item) => new ArrayItem(
                                match (true) {
                                    $item instanceof self => $item->getInstantiationAst(),
                                    default => $f->val($item)
                                },
                            ),
                            $this->parameters[$parameterName],
                        ),
                    ),
                    default => $f->val($this->parameters[$parameterName]),
                },
                name: new Identifier($parameterName),
            ),
            array_keys($this->parameters),
        );
    }
}
