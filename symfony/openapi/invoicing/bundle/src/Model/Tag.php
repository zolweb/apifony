<?php

namespace AppZolInvoicingPresentationApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use AppZolInvoicingPresentationApiBundle\Format\Int64 as AssertInt64;

class Tag
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $name,
    ) {
    }
}
