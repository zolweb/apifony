<?php

namespace App\Zol\Invoicing\Presentation\Api\Bundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int64 as AssertInt64;
use App\Zol\Invoicing\Presentation\Api\Bundle\Format\Int32 as AssertInt32;

class User
{
    public function __construct(
        #[Assert\NotNull]
        #[AssertInt64]
        #[Assert\Choice(choices: [
        ])]
        public readonly int $id,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $username,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $firstName,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $lastName,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $email,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $password,

        #[Assert\NotNull]
        #[Assert\Choice(choices: [
        ])]
        public readonly string $phone,

        #[Assert\NotNull]
        #[AssertInt32]
        #[Assert\Choice(choices: [
        ])]
        public readonly int $userStatus,
    ) {
    }
}
