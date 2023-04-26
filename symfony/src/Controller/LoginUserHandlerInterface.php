<?php

namespace App\Controller;

interface LoginUserHandlerInterface
{
    /**
     * OperationId: loginUser
     *
     * Logs user into the system
     */
    public function handle(
        ?string $username,
        ?string $password,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}
