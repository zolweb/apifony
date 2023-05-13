<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddress
{
    /**
     * @param array<PetOwnerAddressPhoneNumbers> $phoneNumbers
     */
    public function __construct(
        public readonly ?string $street,
        public readonly ?string $country,
        #[Assert\All(constraints: [
                0,
                ])]
        public readonly ?array $phoneNumbers,
    ) {
    }
}
