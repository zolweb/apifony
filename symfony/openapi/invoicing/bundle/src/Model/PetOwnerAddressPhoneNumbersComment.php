<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbersComment
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $title,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $description,
    ) {
    }
}
