<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format as AssertFormat;

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
