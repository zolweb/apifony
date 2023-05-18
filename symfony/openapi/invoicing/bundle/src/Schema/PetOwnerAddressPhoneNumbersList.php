<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbersList
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $number,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwnerAddressPhoneNumbersListComment $comment,
    ) {
    }
}
