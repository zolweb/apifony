<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbers
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $number,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwnerAddressPhoneNumbersComment $comment,
    ) {
    }
}
