<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class GetInventoryApplicationJsonMediaTypeSchema
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
