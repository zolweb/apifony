<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Schema;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Payload\Format as AssertFormat;

class User
{
    public function __construct(
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

        public readonly int $userStatus,
    ) {
    }
}
