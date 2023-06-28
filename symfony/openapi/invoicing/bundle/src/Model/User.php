<?php

namespace AppZolInvoicingPresentationApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use AppZolInvoicingPresentationApiBundle\Format\Int64 as AssertInt64;
use AppZolInvoicingPresentationApiBundle\Format\Int32 as AssertInt32;

class User
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        public readonly int $id,

        #[Assert\NotNull]
        public readonly string $username,

        #[Assert\NotNull]
        public readonly string $firstName,

        #[Assert\NotNull]
        public readonly string $lastName,

        #[Assert\NotNull]
        public readonly string $email,

        #[Assert\NotNull]
        public readonly string $password,

        #[Assert\NotNull]
        public readonly string $phone,

        #[Assert\NotNull]
        #[AssertInt32]
        public readonly int $userStatus,
    ) {
    }
}
