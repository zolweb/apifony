<?php

namespace App\Controller;

class TagSchema
{
    /**
    */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }
}