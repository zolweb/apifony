<?php

namespace App\Controller;

interface UpdateUserHandler
{
    public function handle(
        string $username,
        UserSchema $dto,
    ): UpdateUser201EmptyResponse;
}
