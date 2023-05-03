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
        ?string $qUsername,
        ?string $qPassword,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}