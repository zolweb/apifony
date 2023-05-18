<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetInventory200ApplicationJsonSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
