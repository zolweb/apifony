<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddress
{
    /**
     * object
    */
    public function __construct(
        public readonly ?string $street,
        public readonly ?string $country,
        public readonly ?array $phoneNumbers,
    ) {
    }
}
