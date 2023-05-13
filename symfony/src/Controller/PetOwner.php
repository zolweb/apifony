<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwner
{
    public function __construct(
        public readonly ?string $firstname,
        public readonly ?string $lastname,
        public readonly ?PetOwnerAddress $address,
    ) {
    }
}
