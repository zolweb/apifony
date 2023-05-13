<?php

namespace App\Command;

class Constraint
{
    public function __construct(
        public readonly string $name,
        public readonly array $parameters,
    ) {
    }

    public function getAnnotation(int $indentation): string
    {
        return sprintf(
            '%s#[%s%s]',
            str_repeat(' ', $indentation * 4),
            $this->name,
            count($this->parameters) > 0 ?
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
                                            '\'%s\'',
                                            str_replace('\'', '\\\'', $this->parameters[$name]),
                                        ),
                                    is_int($this->parameters[$name]) || is_float($this->parameters[$name]) =>
                                        strval($this->parameters[$name]),
                                    is_bool($this->parameters[$name]) =>
                                        $this->parameters[$name] ? 'true' : 'false',
                                    is_array($this->parameters[$name]) =>
                                        sprintf(
                                            "[\n%s%s%s]",
                                            str_repeat(' ', ($indentation + 2) * 4),
                                            implode(
                                                array_map(
                                                    fn ($element) => sprintf(
                                                        "%s,\n",
                                                        match (true) {
                                                            is_string($element) =>
                                                                sprintf(
                                                                    '\'%s\'',
                                                                    str_replace('\'', '\\\'', $element),
                                                                ),
                                                            is_int($element) || is_float($element) =>
                                                                strval($element),
                                                            is_bool($element) =>
                                                                $element ? 'true' : 'false',
                                                            $element instanceOf self =>
                                                                $element->getAnnotation($indentation + 3),
                                                        }
                                                    ),
                                                    array_keys($this->parameters[$name]),
                                                ),
                                            ),
                                            str_repeat(' ', ($indentation + 2) * 4),
                                        ),
                                },
                                count($this->parameters) > 1 ? ",\n" : '',
                            ),
                            array_keys($this->parameters),
                        ),
                    ),
                    count($this->parameters) > 1 ? str_repeat(' ', ($indentation) * 4) : '',
                ) : '',
        );
    }
}