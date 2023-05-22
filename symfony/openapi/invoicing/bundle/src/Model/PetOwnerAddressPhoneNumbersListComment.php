<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

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
