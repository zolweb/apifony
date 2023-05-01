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
        ?string $qUsername = null,
        ?string $qPassword = null,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}
