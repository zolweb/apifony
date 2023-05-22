<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class Petowner
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $firstname,

        #[Assert\NotNull]
        public readonly string $lastname,

        #[Assert\Valid]
        public readonly PetownerAddress $address,
    ) {
    }
}
