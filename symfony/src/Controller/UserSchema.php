<?php

namespace App\Controller;

class UserSchema
{
    /**
    */
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $password,
        public readonly string $phone,
        public readonly int $userStatus,
    ) {
    }
}