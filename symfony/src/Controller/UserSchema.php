<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class UserSchema
{
    /**
    */
    public function __construct(
        #[Int64()]
        public readonly ?int $id = null,
        public readonly ?string $username = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $phone = null,
        #[Int32()]
        public readonly ?int $userStatus = null,
    ) {
    }
}