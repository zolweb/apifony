<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class CategorySchema
{
    /**
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
    ) {
    }
}