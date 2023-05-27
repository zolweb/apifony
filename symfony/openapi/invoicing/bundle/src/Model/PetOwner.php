<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwner
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $firstname,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $lastname,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwnerAddress $address,
    ) {
    }
}
