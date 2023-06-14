<?php

namespace App\Command\Bundle;

use function Symfony\Component\String\u;

class Constraint
{
    /**
     * @param array<string, string|int|float|bool|array<string|int|float|bool|self>> $parameters
     */
    public function __construct(
        private readonly string $name,
        private readonly array $parameters,
        private readonly ?string $formatName = null,
    ) {
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

    public function getAnnotation(int $indentation): string
    {
        return sprintf(
            '%s#[%s%s]',
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
                                                        $item instanceOf self =>
                                                            $item->getInstantiation($indentation + 1),
                                                    }
                                                ),
                                                $this->parameters[$name],
                                            ),
                                        ),
                                        str_repeat(' ', ($indentation) * 4),
                                    ),
                            },
                            count($this->parameters) > 1 ? ",\n" : '',
                        ),
                        array_keys($this->parameters),
                    ),
                ),
                count($this->parameters) > 1 ? str_repeat(' ', ($indentation) * 4) : '',
            ) : '';
    }
}