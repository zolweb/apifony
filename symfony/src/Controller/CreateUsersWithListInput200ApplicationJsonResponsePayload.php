<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUsersWithListInput200ApplicationJsonResponsePayload
{
    /**
    */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $username = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $phone = null,
        public readonly ?int $userStatus = null,
    ) {
    }
}