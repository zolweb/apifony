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
        ?string $qPassword,
        ?string $qUsername,
    ): LoginUser200ApplicationJsonResponse|LoginUser400EmptyResponse;
}