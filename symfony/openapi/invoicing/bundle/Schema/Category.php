<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    public function __construct(
        #[Assert\NotNull]
        #[Int64]
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
