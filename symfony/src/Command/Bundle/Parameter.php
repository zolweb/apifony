<?php

namespace App\Command\Bundle;

class Parameter
{
    public static function build(\App\Command\OpenApi\Parameter $parameter): self
    {
        $parameter = new self();

        return $parameter;
    }

    private function __construct()
    {
    }

    public function getVariableName(): string
    {
        return sprintf(
            '%s%s',
            ['path' => 'p', 'query' => 'q', 'cookie' => 'c', 'header' => 'h'][$this->in],
            ucfirst($this->name),
        );
    }

    public function getRequestCollection(): string
    {
        return ['query' => 'query', 'header' => 'headers', 'cookie' => 'cookies'][$this->in];
    }
}