<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetParentArray
{
    public function __construct(
        public readonly ?int $id-id,

        public readonly ?string $name = '',
    ) {
    }
}
