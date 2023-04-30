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
        ?string $username = null,
        ?string $password = null,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}
