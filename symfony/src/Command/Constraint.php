<?php

namespace App\Command;

class Constraint
{
    public function __construct(
        private string $name,
        private array $parameters,
    ) {
    }
}