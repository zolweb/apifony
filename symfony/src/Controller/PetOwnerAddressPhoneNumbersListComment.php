<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbersListComment
{
    public function __construct(
        public readonly string $title,

        public readonly string $description,
    ) {
    }
}
