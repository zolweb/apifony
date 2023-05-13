<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbers
{
    public function __construct(
        public readonly ?string $number,

        #[Assert\Valid]
        public readonly ?PetOwnerAddressPhoneNumbersComment $comment,
    ) {
    }
}
