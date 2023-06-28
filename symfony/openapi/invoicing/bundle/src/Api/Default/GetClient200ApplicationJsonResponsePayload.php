<?php

namespace AppZolInvoicingPresentationApiBundle\Api\Default;

use Symfony\Component\Validator\Constraints as Assert;

class GetClient200ApplicationJsonResponsePayload
{
    public function __construct(
        #[Assert\NotNull]
        public readonly string $test,
    ) {
    }
}
