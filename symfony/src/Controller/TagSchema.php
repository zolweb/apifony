<?php

namespace App\Controller;

class TagSchema
{
    /**
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
    ) {
    }
}