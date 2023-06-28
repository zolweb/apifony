<?php

namespace AppZolInvoicingPresentationApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PetOwnerAddress
{
    /**
     * @param array<PetOwnerAddressPhoneNumbers> $phoneNumbers
     */
    public function __construct(
        #[Assert\NotNull]
        public readonly string $street,

        #[Assert\NotNull]
        public readonly string $country,

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [
            new Assert\NotNull,
        ])]
        public readonly array $phoneNumbers,
    ) {
    }
}
