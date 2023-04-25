<?php

namespace App\Controller;

interface CreateUserHandler
{
    public function handle(
        UserSchema $dto,
    ): CreateUser201ApplicationJsonResponse;
}
