<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbersListComment
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $title,

        #[Assert\NotNull]
        public readonly string $description,
    ) {
    }
}
