<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    public function __construct(
        #[Assert\NotNull]
        #[Int64]
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
        #[Int32]
        public readonly int $userStatus,
    ) {
    }
}
