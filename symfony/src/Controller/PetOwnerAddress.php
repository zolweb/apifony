<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddress
{
    /**
     * @param ?array<PetOwnerAddressPhoneNumbersList> $phoneNumbers
     */
    public function __construct(
        public readonly ?string $street,

        public readonly ?string $country,

        #[Assert\All(constraints: [
            new Assert\Valid,
        ])]
        public readonly ?array $phoneNumbers,
    ) {
    }
}
