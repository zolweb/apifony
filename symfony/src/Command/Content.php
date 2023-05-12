<?php

namespace App\Command;

class Content
{
    public function __construct(
        public readonly string $type,
        array $data,
    ) {
    }
}