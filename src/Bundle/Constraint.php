<?php

namespace Zol\Ogen\Bundle;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use function Symfony\Component\String\u;

class Constraint
{
    /**
     * @param array<string, string|int|float|bool|null|array<string|int|float|bool|null|self>> $parameters
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
     * @return array<string>
     */
    public function getFormatConstraintClassNames(): array
    {
        $formatConstraintNames = [];

        if ($this->formatName !== null) {
            $formatConstraintNames[] =  (string) u($this->formatName)->camel()->title();
        }

        foreach ($this->parameters as $parameter) {
            if (is_array($parameter)) {
                foreach ($parameter as $value) {
                    if ($value instanceof Constraint && $value->formatName !== null) {
                        $formatConstraintNames[] = (string) u($value->formatName)->camel()->title();
                    }
                }
            }
        }

        return $formatConstraintNames;
    }

    public function getInstantiation(int $indentation): string
    {
        return sprintf(
            '%snew %s%s',
            str_repeat(' ', $indentation * 4),
            $this->name,
            $this->format($indentation),
        );
    }

    public function format(int $indentation): string
    {
        return count($this->parameters) > 0 ?
            sprintf(
                "(%s%s%s)",
                count($this->parameters) > 1 ?
                    sprintf(
                        "\n%s",
                        str_repeat(' ', ($indentation + 1) * 4),
                    ) : '',
                implode(
                    array_map(
                        fn ($name) => sprintf(
                            "%s: %s%s",
                            $name,
                            match (true) {
                                is_string($this->parameters[$name]) =>
                                    sprintf(
                                        $this->name === 'Assert\Regex' && $name === 'pattern' ? '\'/%s/\'' : '\'%s\'',
                                        str_replace('\'', '\\\'', $this->parameters[$name]),
                                    ),
                                is_int($this->parameters[$name]) || is_float($this->parameters[$name]) =>
                                    strval($this->parameters[$name]),
                                is_bool($this->parameters[$name]) =>
                                    $this->parameters[$name] ? 'true' : 'false',
                                is_array($this->parameters[$name]) =>
                                    sprintf(
                                        "[\n%s%s]",
                                        implode(
                                            array_map(
                                                fn ($item) => sprintf(
                                                    "%s%s,\n",
                                                    $item instanceOf self ? '' : str_repeat(' ', ($indentation + 1) * 4),
                                                    match (true) {
                                                        is_string($item) =>
                                                            sprintf(
                                                                '\'%s\'',
                                                                str_replace('\'', '\\\'', $item),
                                                            ),
                                                        is_int($item) || is_float($item) =>
                                                            strval($item),
                                                        is_bool($item) =>
                                                            $item ? 'true' : 'false',
                                                        is_null($item) =>
                                                            'null',
                                                        $item instanceOf self =>
                                                            $item->getInstantiation($indentation + 1),
                                                    }
                                                ),
                                                $this->parameters[$name],
                                            ),
                                        ),
                                        str_repeat(' ', ($indentation) * 4),
                                    ),
                                default => throw new \RuntimeException('Unexpected parameter type'),
                            },
                            count($this->parameters) > 1 ? ",\n" : '',
                        ),
                        array_keys($this->parameters),
                    ),
                ),
                count($this->parameters) > 1 ? str_repeat(' ', ($indentation) * 4) : '',
            ) : '';
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
                    $this->name === 'Assert\Regex' && $parameterName === 'pattern' => $f->val("/{$this->parameters[$parameterName]}/"),
                    is_array($this->parameters[$parameterName]) => new Array_(
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