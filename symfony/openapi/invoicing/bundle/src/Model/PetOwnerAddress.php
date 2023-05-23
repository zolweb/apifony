<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;

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

        #[Assert\NotNull]
        #[Assert\All(constraints: [
            new Assert\Valid,
            new Assert\NotNull,
        ])]
        public readonly array $phoneNumbers,
    ) {
    }
}
