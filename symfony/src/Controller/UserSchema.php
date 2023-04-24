<?php

namespace App\Controller;

class UserSchema
{
    public function __construct(
        int $id,
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $phone,
        int $userStatus,
    ) {
    }
}