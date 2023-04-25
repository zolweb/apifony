<?php

namespace App\Controller;

interface DeleteUserHandler
{
    public function handle(
        string $username,
    ): DeleteUser400EmptyResponse|DeleteUser404EmptyResponse;
}
