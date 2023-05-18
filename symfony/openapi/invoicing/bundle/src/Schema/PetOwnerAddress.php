<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddress
{
    /**
     * @param array<PetOwnerAddressPhoneNumbersList> $phoneNumbers
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $street,

        #[Assert\NotNull]
        public readonly string $country,

        #[Assert\All(constraints: [
            new Assert\Valid,
            new Assert\NotNull,
        ])]
        #[Assert\NotNull]
        public readonly array $phoneNumbers,
    ) {
    }
}
