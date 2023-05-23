<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwner
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $firstname,

        #[Assert\NotNull]
        public readonly string $lastname,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwnerAddress $address,
    ) {
    }
}
