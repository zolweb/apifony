<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class PetOwnerAddress
{
    /**
     * @param array<PetOwnerAddressPhoneNumbers> $phoneNumbers
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $street,

        #[Assert\NotNull]
        public readonly string $country,

        #[Assert\All(constraints: [
            new Assert\Valid,
        ])]
        public readonly array $phoneNumbers,
    ) {
    }
}
