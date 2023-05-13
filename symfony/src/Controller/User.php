<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    public function __construct(
        #[Lol()]
        public readonly ?int $id,
        public readonly ?string $username,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $phone,
        #[Lol()]
        public readonly ?int $userStatus,
    ) {
    }
}
