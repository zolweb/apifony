<?php

namespace AppZolInvoicingPresentationApiBundle\Api\Store;

use Symfony\Component\Validator\Constraints as Assert;

class GetInventory200ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
