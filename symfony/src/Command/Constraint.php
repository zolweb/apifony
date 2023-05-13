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
                    "(\n%s%s%s)",
                    str_repeat(' ', ($indentation + 1) * 4),
                    implode(
                        array_map(
                            fn ($name) => sprintf(
                                "%s: %s,\n",
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
                                            '[%s]',
                                            'Lol',
                                        ),
                                }
                            ),
                            array_keys($this->parameters),
                        ),
                    ),
                    str_repeat(' ', ($indentation) * 4),
                ) : '',
        );
    }
}