<?php

namespace AppZolInvoicingPresentationApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddressPhoneNumbers
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $number,

        #[Assert\Valid]
        #[Assert\NotNull]
        public readonly PetOwnerAddressPhoneNumbersComment $comment,
    ) {
    }
}
