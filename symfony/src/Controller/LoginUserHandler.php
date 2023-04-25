<?php

namespace App\Controller;

interface LoginUserHandler
{
    public function handle(
        ?string $username,
        ?string $password,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}
