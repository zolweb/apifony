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
        public readonly ?array $phoneNumbers,
    ) {
    }
}
